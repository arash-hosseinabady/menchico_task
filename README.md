# Menchico Mini API

A mini API service for **Menchico** that handles match result reporting and leaderboard functionality. Built with **PHP 8.2+**, **CakePHP 5**, **MySQL/MariaDB**, and **Redis**.

---

## Features

- **POST /matches/report** → Record match results with idempotency handling
- **GET /leaderboard** → Retrieve leaderboard data with Redis fallback support
- Token-based authentication with custom header validation
- Rate limiting (**60 requests/minute per user-IP combination**)
- Redis-based leaderboards with SQL fallback

---

## Requirements

- PHP 8.2+
- Composer
- MySQL/MariaDB 5.7+
- Redis 5+
- CakePHP 5

---

## Installation

```bash
# Clone the repository
git clone https://github.com/your-org/menchico-mini.git
cd menchico-mini

# Install dependencies
composer install
```

Configure environment variables in `.env`:

```ini
APP_NAME="menchico-mini"
APP_ENV="dev"
APP_DEBUG=true
APP_KEY="your-secure-key-here"

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=menchico
DB_USER=root
DB_PASS=secret

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

SEASON_ID=2025S3
```

Run migrations and seed data:

```bash
bin/cake migrations migrate
bin/cake migrations seed   # optional
```

---

## Cache Configuration

```php
'Cache' => [
    'default' => [
        'className' => RedisEngine::class,
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'port' => env('REDIS_PORT', 6379),
        'timeout' => 1,
        'persistent' => false,
        'prefix' => 'menchico_',
    ],
    'leaderboard' => [
        'className' => RedisEngine::class,
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'port' => env('REDIS_PORT', 6379),
        'timeout' => 1,
        'prefix' => 'lb_',
    ],
    'ratelimit' => [
        'className' => RedisEngine::class,
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'port' => env('REDIS_PORT', 6379),
        'timeout' => 1,
        'prefix' => 'rl_',
        'duration' => 70,
    ],
]
```

---

## API Endpoints

### **POST /matches/report**

Record a match result with idempotency protection.

**Headers:**
- `Authorization: Bearer {api_token}`
- `MenschAgent: {client_identifier}`

**Request Body:**
```json
{
  "match_id": "uuid-string",
  "user_id": 123,
  "result": "win|loss",
  "points": 50
}
```

**Response:**
```json
{
  "ok": true,
  "user_id": 123,
  "new_trophy": 1450,
  "applied": true
}
```

---

### **GET /leaderboard**

Retrieve leaderboard data for a given scope.

**Query Parameters:**
- `scope` (required): `daily`, `weekly`, or `season`
- `limit` (optional, default `10`): Number of entries (1-100)
- `user_id` (required): ID of the requesting user

**Response:**
```json
{
  "ok": true,
  "scope": "weekly",
  "top": [
    {"user_id": 7, "score": 2100, "rank": 1},
    {"user_id": 123, "score": 1950, "rank": 2}
  ],
  "me": {"user_id": 123, "score": 1950, "rank": 2}
}
```

---

## Database Schema

### **Users Table**
- `id` (PK)
- `username` VARCHAR(64) UNIQUE
- `password_hash` TEXT NULL
- `api_token` VARCHAR(64) UNIQUE NOT NULL
- `current_season_trophy` INT NOT NULL DEFAULT 0
- `created` DATETIME
- `modified` DATETIME

### **Match Reports Table**
- `id` (PK)
- `match_id` VARCHAR(64) UNIQUE NOT NULL
- `user_id` (FK)
- `result` ENUM('win','loss')
- `points` INT NOT NULL
- `created` DATETIME

### **Trophy History Table**
- `id` (PK)
- `user_id` (FK)
- `delta` INT NOT NULL
- `reason` VARCHAR(64) NOT NULL
- `created` DATETIME

---

## Redis Keys

**Leaderboards (ZSET):**
- `lb:daily:YYYY-MM-DD`
- `lb:weekly:YYYY-WW`
- `lb:season:2025S3`

**Rate Limit (STRING counters):**
- `rl:report:{user_id}:{ip}:{yyyyMMddHHmm}`

---

## Business Rules

- **Idempotency:** Duplicate `match_id` → `applied: false`
- **Atomicity:** All DB changes within transactions
- **Fallback:** SQL fallback when Redis unavailable
- **Validation:** Rejects invalid enums, missing fields, negative points
- **Security:** Users may only report their own results

---

## Testing

Key scenarios:
- Idempotent match reporting
- Rate limiting → **429 responses**
- Missing `MenschAgent` header → **400 responses**
- Leaderboard fallback without Redis

Run tests:
```bash
vendor/bin/phpunit
```

---

## Design Notes

### Trade-offs
- CakePHP → rapid development (ORM + validation)
- Redis fallback → high availability
- Token authentication → simplicity

### Error Handling
- Comprehensive validation with meaningful error responses
- Transaction rollback on failures
- Graceful degradation without Redis

### Future Enhancements
- Real-time leaderboard updates (WebSockets)
- Advanced analytics & reporting
- Microservices for scalability
- Smarter caching strategies

---

## Running the Application

Start dependencies (MySQL, Redis), then run:
```bash
bin/cake server
```

API available at: **http://localhost:8765**

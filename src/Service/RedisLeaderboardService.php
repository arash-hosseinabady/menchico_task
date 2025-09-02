<?php
declare(strict_types=1);

namespace App\Service;

use InvalidArgumentException;
use Redis;

final class RedisLeaderboardService
{
    private Redis $redis;

    /**
     * @param string|null $host
     * @param int|null $port
     * @param float|null $timeout
     * @throws \RedisException
     */
    public function __construct(?string $host = null, ?int $port = null, ?float $timeout = null)
    {
        $host = $host ?? env('REDIS_HOST', '127.0.0.2');
        $port = $port ?? (int)env('REDIS_PORT', 6379);
        $timeout = $timeout ?? 1.0;

        $this->redis = new Redis();
        $this->redis->connect($host, $port, $timeout);
    }

    /**
     * @param string $scope
     * @param int $userId
     * @return string
     */
    private function makeKey(string $scope, int $userId): string
    {
        return match ($scope) {
            'daily' => 'daily:' . date('Y-m-d') . ':' . $userId,
            'weekly' => 'weekly:' . sprintf('%04d-%02d', (int)date('o'), (int)date('W')) . ':' . $userId,
            'season' => 'season:' . env('SEASON_ID', '2025S3') . ':' . $userId,
            default => throw new InvalidArgumentException('Invalid scope'),
        };
    }

    /**
     * @param string $scope
     * @param int $userId
     * @param int $delta
     * @return void
     * @throws \RedisException
     */
    public function incrementScore(string $scope, int $userId, int $delta): void
    {
        if ($delta <= 0) {
            return;
        }
        $key = $this->makeKey($scope, $userId);
        $this->redis->incrBy($key, $delta);
    }

    /**
     * @param string $pattern
     * @param int $limit
     * @return array<int, array{user_id:int, score:int, rank:int}>
     */
    public function getTop(string $pattern, int $limit = 10): array
    {
        $it = null;
        $scores = [];

        while ($keys = $this->redis->scan($it, $pattern, 100)) {
            foreach ($keys as $key) {
                $parts = explode(':', $key);
                $userId = (int)end($parts);
                $score = (int)$this->redis->get($key);
                $scores[$userId] = $score;
            }
        }

        arsort($scores);

        $rank = 1;
        $out = [];
        foreach (array_slice($scores, 0, $limit, true) as $uid => $sc) {
            $out[] = [
                'user_id' => $uid,
                'score' => $sc,
                'rank' => $rank++,
            ];
        }

        return $out;
    }

    /**
     * @param string $pattern
     * @param int $userId
     * @return array{user_id:int, score:int, rank:int}|null
     */
    public function getMe(string $pattern, int $userId): ?array
    {
        $top = $this->getTop($pattern, PHP_INT_MAX);
        foreach ($top as $row) {
            if ($row['user_id'] === $userId) {
                return $row;
            }
        }

        return null;
    }
}

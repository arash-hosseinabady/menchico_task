<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Service\RedisLeaderboardService;
use Cake\Cache\Cache;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\LeaderboardController Test Case
 *
 * @link \App\Controller\LeaderboardController
 */
class LeaderboardControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Users',
        'app.MatchReports',
        'app.TrophyHistory',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $cacheConfig = Cache::getConfig('leaderboard');
        $redisService = new RedisLeaderboardService();
        $pattern = $cacheConfig['prefix'] . 'daily:' . date('Y-m-d') . ':*';
        $redisService->deleteAllKeysByPattern($pattern);
    }

    public function testGetLeaderboardDailyWithData(): void
    {
        Cache::increment('daily:' . date('Y-m-d') . ':' . 1, 100, 'leaderboard');
        Cache::increment('daily:' . date('Y-m-d') . ':' . 2, 200, 'leaderboard');

        $this->configRequest([
            'headers' => [
                'Authorization' => 'Bearer token-abc-123',
                'MenschAgent' => 'unity',
            ],
        ]);

        $this->get('/leaderboard?scope=daily&limit=2&user_id=1');
        $this->assertResponseOk();

        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertTrue($data['ok']);
        $this->assertEquals('daily', $data['scope']);
        $this->assertCount(2, $data['top']);
        $this->assertEquals(1, $data['me']['user_id']);
    }

    public function testGetLeaderboardDailyWithNoData(): void
    {
        $this->configRequest([
            'headers' => [
                'Authorization' => 'Bearer token-abc-123',
                'MenschAgent' => 'unity',
            ],
        ]);

        $this->get('/leaderboard?scope=daily&limit=2&user_id=10');
        $this->assertResponseOk();

        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertTrue($data['ok']);
        $this->assertEquals('daily', $data['scope']);
        $this->assertEmpty($data['top']);
        $this->assertNull($data['me']);
    }

    public function testGetLeaderboardInvalidScope(): void
    {
        $this->get('/leaderboard?scope=invalid&limit=10&user_id=1');
        $this->assertResponseCode(400);
    }

    public function testFallbackToSqlWhenRedisFails(): void
    {
        $this->configRequest([
            'headers' => [
                'Authorization' => 'Bearer token-abc-123',
                'MenschAgent' => 'unity',
            ],
        ]);

        $this->get('/leaderboard?scope=season&limit=5&user_id=1');
        $this->assertResponseOk();
    }
}

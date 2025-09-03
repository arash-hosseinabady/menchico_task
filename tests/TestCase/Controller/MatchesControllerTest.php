<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Service\RedisLeaderboardService;
use Cake\Cache\Cache;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\MatchesController Test Case
 *
 * @link \App\Controller\MatchesController
 */
class MatchesControllerTest extends TestCase
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

        $redisService = new RedisLeaderboardService();
        $cacheConfig = Cache::getConfig('leaderboard');
        $pattern = $cacheConfig['prefix'] . 'daily:' . date('Y-m-d') . ':*';
        $redisService->deleteAllKeysByPattern($pattern);

        $cacheConfig = Cache::getConfig('ratelimit');
        $pattern = $cacheConfig['prefix'] . '*';
        $redisService->deleteAllKeysByPattern($pattern);
    }

    public function testMissingMenschAgentHeader(): void
    {
        $this->configRequest([
            'headers' => [
                'Authorization' => 'Bearer token-abc-123',
            ],
        ]);
        $this->post('/matches/report', [
            'match_id' => 1,
            'user_id' => 1,
            'result' => 'win',
            'points' => 50,
        ]);
        $this->assertResponseCode(400);
    }

    public function testUnauthorizedUserMismatch(): void
    {
        $this->configRequest([
            'headers' => [
                'Authorization' => 'Bearer token-abc-1234',
                'MenschAgent' => 'unity',
            ],
        ]);
        $this->post('/matches/report', [
            'match_id' => 2,
            'result' => 'win',
            'points' => 50,
        ]);
        $this->assertResponseCode(401);
    }

    public function testReportWinAndIdempotency(): void
    {
        $this->configRequest([
            'headers' => [
                'Authorization' => 'Bearer token-abc-123',
                'MenschAgent' => 'unity',
            ],
        ]);

        $this->post('/matches/report', [
            'match_id' => 3,
            'user_id' => 1,
            'result' => 'win',
            'points' => 100,
        ]);
        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertTrue($data['applied']);

        $this->configRequest([
            'headers' => [
                'Authorization' => 'Bearer token-abc-123',
                'MenschAgent' => 'unity',
            ],
        ]);

        $this->post('/matches/report', [
            'match_id' => 3,
            'user_id' => 1,
            'result' => 'win',
            'points' => 200,
        ]);
        $this->assertResponseOk();
        $data = json_decode((string)$this->_response->getBody(), true);
        $this->assertFalse($data['applied']);
    }

    public function testRateLimitExceeded(): void
    {
        for ($i = 1; $i <= 61; $i++) {
            $this->configRequest([
                'headers' => [
                    'Authorization' => 'Bearer token-abc-123',
                    'MenschAgent' => 'unity',
                ],
            ]);

            $this->post('/matches/report', [
                'match_id' => $i,
                'user_id' => 1,
                'result' => 'loss',
                'points' => 0,
            ]);
        }

        $this->assertResponseCode(429);
    }
}

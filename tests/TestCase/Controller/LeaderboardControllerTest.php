<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Service\RedisLeaderboardService;
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
        $lb = new RedisLeaderboardService();
        $lb->incrementScore('daily', 1, 100);
        $lb->incrementScore('daily', 2, 200);
        $lb->incrementScore('daily', 3, 50);
    }

    public function testGetLeaderboardDaily(): void
    {
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
        $this->assertCount(1, $data['top']);
        $this->assertEquals(1, $data['me']['user_id']);
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

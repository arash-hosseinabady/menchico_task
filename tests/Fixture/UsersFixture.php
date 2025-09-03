<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'username' => 'tester1',
                'password_hash' => null,
                'api_token' => 'token-abc-123',
                'current_season_trophy' => 1000,
                'created' => '2025-09-01 10:00:00',
                'modified' => '2025-09-01 10:00:00',
            ],
            [
                'id' => 2,
                'username' => 'tester2',
                'password_hash' => null,
                'api_token' => 'token-def-456',
                'current_season_trophy' => 800,
                'created' => '2025-09-01 11:00:00',
                'modified' => '2025-09-01 11:00:00',
            ],
            [
                'id' => 3,
                'username' => 'tester3',
                'password_hash' => null,
                'api_token' => 'token-ghi-789',
                'current_season_trophy' => 500,
                'created' => '2025-09-01 12:00:00',
                'modified' => '2025-09-01 12:00:00',
            ],
        ];
        parent::init();
    }
}

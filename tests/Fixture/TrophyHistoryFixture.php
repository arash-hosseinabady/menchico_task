<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TrophyHistoryFixture
 */
class TrophyHistoryFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'trophy_history';
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
                'user_id' => 1,
                'delta' => 50,
                'reason' => 'match:1',
                'created' => '2025-09-01 09:05:00',
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'delta' => -30,
                'reason' => 'match:2',
                'created' => '2025-09-01 09:15:00',
            ],
        ];
        parent::init();
    }
}

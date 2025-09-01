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
                'delta' => 1,
                'reason' => 'Lorem ipsum dolor sit amet',
                'created' => '2025-09-01 20:09:24',
            ],
        ];
        parent::init();
    }
}

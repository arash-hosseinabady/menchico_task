<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MatchReportsFixture
 */
class MatchReportsFixture extends TestFixture
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
                'match_id' => 1,
                'user_id' => 1,
                'result' => 'win',
                'points' => 50,
                'created' => '2025-09-01 09:00:00',
            ],
            [
                'id' => 2,
                'match_id' => 2,
                'user_id' => 2,
                'result' => 'loss',
                'points' => 30,
                'created' => '2025-09-01 09:10:00',
            ],
        ];
        parent::init();
    }
}

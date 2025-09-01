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
                'match_id' => 'Lorem ipsum dolor sit amet',
                'user_id' => 1,
                'result' => 'Lorem ip',
                'points' => 1,
                'created' => '2025-09-01 20:08:28',
            ],
        ];
        parent::init();
    }
}

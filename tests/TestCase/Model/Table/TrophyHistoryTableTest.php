<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TrophyHistoryTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TrophyHistoryTable Test Case
 */
class TrophyHistoryTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TrophyHistoryTable
     */
    protected $TrophyHistory;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.TrophyHistory',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('TrophyHistory') ? [] : ['className' => TrophyHistoryTable::class];
        $this->TrophyHistory = $this->getTableLocator()->get('TrophyHistory', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->TrophyHistory);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\TrophyHistoryTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\TrophyHistoryTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

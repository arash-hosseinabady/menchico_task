<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MatchReportsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MatchReportsTable Test Case
 */
class MatchReportsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MatchReportsTable
     */
    protected $MatchReports;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.MatchReports',
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
        $config = $this->getTableLocator()->exists('MatchReports') ? [] : ['className' => MatchReportsTable::class];
        $this->MatchReports = $this->getTableLocator()->get('MatchReports', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->MatchReports);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\MatchReportsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\MatchReportsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

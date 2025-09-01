<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateMatchReports extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $this->table('match_reports')
            ->addColumn('match_id', 'string', ['limit' => 64])
            ->addColumn('user_id', 'integer')
            ->addColumn('result', 'string', ['limit' => 10])
            ->addColumn('points', 'integer')
            ->addColumn('created', 'datetime')
            ->addIndex(['match_id'], ['unique' => true])
            ->create();
    }
}

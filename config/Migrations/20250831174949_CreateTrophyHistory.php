<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateTrophyHistory extends BaseMigration
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
        $this->table('trophy_history')
            ->addColumn('user_id', 'integer')
            ->addColumn('delta', 'integer')
            ->addColumn('reason', 'string', ['limit' => 64])
            ->addColumn('created', 'datetime')
            ->addIndex(['user_id', 'created'])
            ->create();
    }
}

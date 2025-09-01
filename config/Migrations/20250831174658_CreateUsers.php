<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateUsers extends BaseMigration
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
        $this->table('users')
            ->addColumn('username', 'string', ['limit' => 64])
            ->addColumn('password_hash', 'text', ['null' => true])
            ->addColumn('api_token', 'string', ['limit' => 64])
            ->addColumn('current_season_trophy', 'integer', ['default' => 0])
            ->addTimestamps()
            ->addIndex(['username'], ['unique' => true])
            ->addIndex(['api_token'], ['unique' => true])
            ->create();
    }
}

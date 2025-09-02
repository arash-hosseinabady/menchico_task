<?php
declare(strict_types=1);

use Cake\Utility\Security;
use Migrations\BaseSeed;

/**
 * Users seed.
 */
class UsersSeed extends BaseSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/migrations/4/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $table = $this->table('users');
        $table->truncate();

        $data = [
            [
                'username' => 'user1',
                'password_hash' => password_hash('user1123', PASSWORD_DEFAULT),
                'api_token' => Security::hash(Security::randomBytes(32), 'sha256'),
                'created' => date('Y-m-d H:i:s'),
                'updated' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'user2',
                'password_hash' => password_hash('user2123', PASSWORD_DEFAULT),
                'api_token' => Security::hash(Security::randomBytes(32), 'sha256'),
                'created' => date('Y-m-d H:i:s'),
                'updated' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'user3',
                'password_hash' => password_hash('user3123', PASSWORD_DEFAULT),
                'api_token' => Security::hash(Security::randomBytes(32), 'sha256'),
                'created' => date('Y-m-d H:i:s'),
                'updated' => date('Y-m-d H:i:s'),
            ],
        ];

        $table->insert($data)->save();
    }
}

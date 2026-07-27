<?php declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

/**
 * Class UserSeeder
 */
final class UserSeeder extends AbstractSeed
{
    /**
     *
     */
    public function run(): void
    {
        try {
            $username = 'admin';

            $existing = $this->fetchRow( "SELECT id FROM users WHERE username = '" . $username . "'" );

            if ( $existing ) {
                return;
            }

            $options = ['cost' => 12];

            $data = [
                [
                    'username' => $username,
                    'password' => password_hash( 'test', PASSWORD_DEFAULT, $options ),
                    'email' => 'admin@localhost'
                ]
            ];

            $item = $this->table( 'users' );
            $item->insert( $data )->saveData();
        } catch ( Exception $exception ) {
            echo $exception->getMessage();
        }
    }
}

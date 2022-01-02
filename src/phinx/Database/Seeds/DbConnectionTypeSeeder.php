<?php declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

/**
 * Class DbConnectionTypeSeeder
 */
final class DbConnectionTypeSeeder extends AbstractSeed
{
    /**
     *
     */
    public function run(): void
    {
        try {
            $data = [
                ['driver' => 'mysql'],
                ['driver' => 'postgresql'],
            ];

            $item = $this->table( 'db_connection_type' );
            $item->insert( $data )->saveData();
        } catch ( Exception $exception ) {
            echo $exception->getMessage();
        }
    }
}

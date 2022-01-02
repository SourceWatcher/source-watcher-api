<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Class CreateDbConnectionTypeTable
 */
final class CreateDbConnectionTypeTable extends AbstractMigration
{
    /**
     *
     */
    public function up(): void
    {
        try {
            $table = $this->table( "db_connection_type", ["id" => true] );
            $table
                ->addColumn( "driver", "text", ["length" => 255, "null" => false] )
                ->save();
        } catch ( Exception $exception ) {
            echo $exception->getMessage();
        }
    }
}

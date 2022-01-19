<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Class CreateRefreshTokenTable
 */
final class CreateRefreshTokenTable extends AbstractMigration
{
    /**
     *
     */
    public function up(): void
    {
        try {
            $table = $this->table( 'refresh_token', ['id' => true] );
            $table
                ->addColumn( 'user_id', 'integer' )
                ->addColumn( 'value', 'text', ['length' => 255, 'null' => false] )
                ->save();

            $table->addForeignKey( 'user_id', 'users', 'id' )->save();
        } catch ( Exception $exception ) {
            echo $exception->getMessage();
        }
    }
}

<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Class CreateItemTable
 */
final class CreateItemTable extends AbstractMigration
{
    /**
     *
     */
    public function up(): void
    {
        try {
            $table = $this->table("item", ["id" => true]);
            $table
                ->addColumn("name", "text", ["length" => 50])
                ->addColumn("description", "text", ["length" => 200, "null" => true])
                ->save();
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }
}

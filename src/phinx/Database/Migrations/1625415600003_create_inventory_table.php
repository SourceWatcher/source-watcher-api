<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Class CreateInventoryTable
 */
final class CreateInventoryTable extends AbstractMigration
{
    /**
     *
     */
    public function up(): void
    {
        try {
            $table = $this->table("inventory", ["id" => true]);
            $table
                ->addColumn("item_id", "integer")
                ->addColumn("units", "integer")
                ->addColumn("expiration_date", "date")
                ->save();

            $table->addForeignKey("item_id", "item", "id")->save();
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }
}

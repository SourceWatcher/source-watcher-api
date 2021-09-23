<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Class CreateItemTypePerItemTable
 */
final class CreateItemTypePerItemTable extends AbstractMigration
{
    /**
     *
     */
    public function up(): void
    {
        try {
            $table = $this->table("item_type_per_item", ["id" => true]);
            $table
                ->addColumn("item_id", "integer")
                ->addColumn("item_type_id", "integer")
                ->save();

            $table->addForeignKey("item_id", "item", "id")->save();
            $table->addForeignKey("item_type_id", "item_type", "id")->save();
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }
}

<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Class CreateItemTypeTable
 */
final class CreateItemTypeTable extends AbstractMigration
{
    /**
     *
     */
    public function up(): void
    {
        try {
            $table = $this->table("item_type", ["id" => true]);
            $table
                ->addColumn("name", "text", ["length" => 50])
                ->addColumn("description", "text", ["length" => 200])
                ->save();
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }
}

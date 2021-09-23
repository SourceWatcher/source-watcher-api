<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

/**
 * Class ItemSeeder
 */
final class ItemSeeder extends AbstractSeed
{
    /**
     *
     */
    public function run(): void
    {
        try {
            $data = [
                ["name" => "Canned black beans"],
                ["name" => "Canned pinto beans"],
                ["name" => "Canned red beans"]
            ];

            $item = $this->table("item");
            $item->insert($data)->saveData();
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }
}

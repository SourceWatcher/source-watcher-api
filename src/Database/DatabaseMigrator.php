<?php declare(strict_types=1);

namespace Coco\SourceWatcherApi\Database;

use Phinx\Config\Config;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Coco\SourceWatcherApi\Framework\EnvironmentVariables;

/**
 * Class DatabaseMigrator
 * @package Coco\SourceWatcherApi\Database
 */
class DatabaseMigrator
{
    use EnvironmentVariables;

    /**
     * DatabaseMigrator constructor.
     */
    public function __construct()
    {
        $this->loadEnvironmentVariables();
    }

    /**
     * Allows getting the database config array.
     * @return array
     */
    public function getDatabaseConfig(): array
    {
        return [
            "adapter" => $_ENV["DB_ADAPTER"],
            "host" => $_ENV["DB_HOST"],
            "user" => $_ENV["DB_USER"],
            "pass" => $_ENV["DB_PASS"],
            "port" => $_ENV["DB_PORT"],
            "charset" => $_ENV["DB_CHARSET"]
        ];
    }

    /**
     * Allows getting a manager object.
     * @param string $databaseName
     * @return Manager
     */
    private function getManager(string $databaseName): Manager
    {
        $base = join("/", [__DIR__, "..", "phinx"]);

        $migrations = "$base/Database/Migrations";
        $seeds = "$base/Database/Seeds";

        $config = $this->getDatabaseConfig() + ["name" => $databaseName];

        $stringInput = "";
        $input = new StringInput($stringInput);

        return new Manager(
            new Config([
                "paths" => [
                    "migrations" => $migrations,
                    "seeds" => $seeds,
                ],
                "environments" => [
                    "default_migration_table" => "phinxlog",
                    "default_environment" => "development",
                    "development" => $config
                ]
            ]),
            $input,
            new NullOutput()
        );
    }

    /**
     * Allows migrating the database.
     * @param string $databaseName
     */
    public function migrateDatabase( string $databaseName ): void
    {
        $manager = $this->getManager( $databaseName );
        $manager->migrate( "development" );
    }

    /**
     * Allows seeding the database.
     * @param string $databaseName
     * @param string|null $seed
     */
    public function seedDatabase( string $databaseName, string $seed = null ): void
    {
        $manager = $this->getManager( $databaseName );
        $manager->seed( "development", $seed );
    }
}

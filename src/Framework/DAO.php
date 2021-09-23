<?php

namespace Coco\SourceWatcherApi\Framework;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception as DbalException;
use Coco\SourceWatcherApi\Framework\Exception as FrameworkException;

/**
 * Class DAO
 * @package Coco\SourceWatcherApi\Framework
 */
class DAO
{
    /**
     * DAO constructor.
     */
    public function __construct()
    {
        $this->loadEnvironmentVariables();
    }

    use EnvironmentVariables;

    /**
     * Allows getting a database connection object.
     * @return Connection
     * @throws Exception
     */
    protected function getConnection(): Connection
    {
        $connectionParams = array(
            "host" => $_ENV["DB_HOST"],
            "dbname" => $_ENV["DB_NAME"],
            "user" => $_ENV["DB_USER"],
            "password" => $_ENV["DB_PASS"],
            "driver" => $_ENV["DB_DRIVER"],
        );

        try {
            return DriverManager::getConnection($connectionParams);
        } catch (DbalException $e) {
            throw new FrameworkException(
                sprintf("Something went wrong trying to get the connection: %s", $e->getMessage())
            );
        }
    }
}

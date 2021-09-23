<?php

namespace Coco\SourceWatcherApi\Framework;

use Dotenv\Dotenv;

/**
 * Trait EnvironmentVariables
 * @package Coco\SourceWatcherApi\Framework
 */
trait EnvironmentVariables
{
    /**
     *
     */
    function loadEnvironmentVariables(): void
    {
        $envPath = join("/", [__DIR__, "..", ".."]);

        $dotEnv = Dotenv::createImmutable($envPath);
        $dotEnv->load();
    }
}

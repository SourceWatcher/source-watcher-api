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
        $envFile = $envPath . '/.env';

        if (!is_file($envFile)) {
            return; // No .env (e.g. Docker): rely on $_ENV already set by index.php or getenv()
        }

        $dotEnv = Dotenv::createImmutable($envPath);
        $dotEnv->load();
    }
}

<?php

namespace Coco\SourceWatcherApi\Security\v1;

use Coco\SourceWatcherApi\Framework\Controller;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class CredentialsController
 * @package Coco\SourceWatcherApi\Security\v1
 */
class CredentialsController extends Controller
{
    /**
     * @var Logger
     */
    private Logger $log;

    /**
     * JWKSController constructor.
     */
    public function __construct()
    {
        $logPath = join('/', [__DIR__, '..', '..', '..', 'logs', time() . '.log']);

        $this->log = new Logger(JWKSController::class);
        $this->log->pushHandler(new StreamHandler($logPath, Logger::INFO));

        parent::__construct();
    }

    /**
     * Allows processing the request to the endpoint.
     * @param string $requestMethod
     * @param array $extraOptions
     */
    public function processRequest(string $requestMethod, array $extraOptions): void
    {
        // TODO: Implement processRequest() method.
    }
}

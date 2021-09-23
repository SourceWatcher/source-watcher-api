<?php

namespace Coco\SourceWatcherApi\Security\v1;

use Coco\SourceWatcherApi\Framework\Controller;
use Coco\SourceWatcherApi\Framework\ResponseCodes;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class JWKSController
 * @package Coco\SourceWatcherApi\Security
 */
class JWKSController extends Controller
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
     * It will return the JSON Web Key Set if requested via GET, or an error for any other case.
     * @param string $requestMethod
     * @param array $extraOptions
     */
    public function processRequest(string $requestMethod, array $extraOptions): void
    {
        if ($requestMethod === 'GET') {
            header(ResponseCodes::OK);

            echo json_encode(['keys' => [$this->getJWK($extraOptions)]]);
        } else {
            header(ResponseCodes::BAD_REQUEST);

            echo json_encode(['message' => 'unsupported method']);
        }
    }

    /**
     * Allows returning the JSON Web Key.
     * @return array
     */
    private function getJWK(): array
    {
        $details = openssl_pkey_get_details(
            openssl_pkey_get_public(
                file_get_contents(
                    join('/', [__DIR__, 'keys', 'current', 'public.pem'])
                )
            )
        );

        $rsa = $details['rsa'];

        return [
            'kty' => 'RSA',
            'e' => base64_encode($rsa['e']),
            'use' => 'sig',
            'kid' => '',
            'alg' => '',
            'n' => base64_encode($rsa['n'])
        ];
    }
}

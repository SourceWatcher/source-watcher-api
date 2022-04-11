<?php declare(strict_types=1);

namespace Coco\SourceWatcherApi\Security\JWT;

use Coco\SourceWatcherApi\Security\Constants as SecurityConstants;
use Coco\SourceWatcherApi\Security\JWKS\JWKSHelper;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use JetBrains\PhpStorm\ArrayShape;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class JWTHelper
{
    private Logger $log;

    public function __construct()
    {
        $logPath = join('/', [__DIR__, '..', '..', '..', 'logs', time() . '.log']);

        $this->log = new Logger(JWTHelper::class);
        $this->log->pushHandler(new StreamHandler($logPath, Logger::ERROR));
    }

    #[ArrayShape(['data' => "int[]", "iss" => "mixed", "aud" => "mixed", "iat" => "false|int", "eat" => "false|int"])]
    public function getPayload(int $userId): array
    {
        return [
            'data' => ['userId' => $userId],
            "iss" => $_SERVER['HTTP_HOST'],
            "aud" => $_SERVER['HTTP_HOST'],
            "iat" => strtotime('now'),
            "eat" => strtotime(SecurityConstants::JWT_EXPIRATION_TIME)
        ];
    }

    public function jwtIsValid(string $jwt): bool
    {
        try {
            $jwksHelper = new JWKSHelper();

            $jwtDecoded = JWT::decode($jwt, new Key($jwksHelper->getPublicKey(), SecurityConstants::ALGORITHM));

            $iss = $jwtDecoded->iss;
            $iss_is_valid = !empty($iss) && $iss === $_SERVER['HTTP_HOST'];

            if (!$iss_is_valid) {
                return false;
            }

            $aud = $jwtDecoded->aud;
            $aud_is_valid = !empty($aud) && $aud === $_SERVER['HTTP_HOST'];

            if (!$aud_is_valid) {
                return false;
            }

            $iat = $jwtDecoded->iat;
            $iat_is_valid = !empty($iat) && $iat < time();

            if (!$iat_is_valid) {
                return false;
            }

            $eat = $jwtDecoded->eat;
            $eat_is_valid = !empty($eat) && $eat > time();

            if (!$eat_is_valid) {
                return false;
            }
        } catch (Exception $exception) {
            $this->log->error(
                sprintf(
                    'Something went wrong trying to verify if the JWT is valid: %s', $exception->getMessage()
                )
            );

            return false;
        }

        return true;
    }
}

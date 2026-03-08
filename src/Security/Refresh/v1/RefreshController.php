<?php declare(strict_types=1);

namespace Coco\SourceWatcherApi\Security\Refresh\v1;

use Coco\SourceWatcherApi\Framework\ApiResponse;
use Coco\SourceWatcherApi\Framework\Controller;
use Coco\SourceWatcherApi\Framework\ResponseCodes;
use Coco\SourceWatcherApi\Security\Constants as SecurityConstants;
use Coco\SourceWatcherApi\Security\JWKS\JWKSHelper;
use Coco\SourceWatcherApi\Security\JWT\JWTHelper;
use Coco\SourceWatcherApi\Security\Refresh\RefreshTokenDAO;
use Coco\SourceWatcherApi\Security\Refresh\RefreshTokenHelper;
use Exception as CoreException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class RefreshController extends Controller
{
    use ApiResponse;

    private Logger $log;

    public function __construct()
    {
        $logPath = join('/', [__DIR__, '..', '..', '..', '..', 'logs', time() . '.log']);

        $this->log = new Logger(RefreshController::class);
        $this->log->pushHandler(new StreamHandler($logPath, Logger::INFO));

        parent::__construct();
    }

    public function processRequest(string $requestMethod, array $extraOptions): void
    {
        if ($requestMethod == 'POST') {
            $response = $this->refreshToken();
        } else {
            $response = $this->notFoundResponse();
        }

        header($response['status_code_header']);

        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function refreshToken(): array
    {
        try {
            $jwksHelper = new JWKSHelper();

            $jwtDecoded = JWT::decode($this->requestData['access_token'], new Key($jwksHelper->getPublicKey(), SecurityConstants::ALGORITHM));

            $data = $jwtDecoded->data;

            $userId = $data->userId;

            $refreshTokenDao = new RefreshTokenDAO();

            $refreshToken = $refreshTokenDao->getRefreshToken($userId, $this->requestData['refresh_token']);

            if (empty($refreshToken)) {
                // The refresh token didn't match the user id or didn't exist

                $this->log->info('Invalid data was provided for the refresh token logic');
                $this->log->info(sprintf('Access token = %s', $this->requestData['access_token']));
                $this->log->info(sprintf('Refresh token = %s', $this->requestData['refresh_token']));
            }

            $refreshTokenDao->deleteRefreshToken($userId, $this->requestData['refresh_token']);

            $jwtHelper = new JWTHelper();

            $key = $jwksHelper->getJWK();

            $newAccessToken = JWT::encode($jwtHelper->getPayload($userId), $jwksHelper->getPrivateKey(), SecurityConstants::ALGORITHM, $key['kid']);

            $newRefreshToken = RefreshTokenHelper::getRefreshToken();

            $refreshTokenDao->insertRefreshToken($userId, $newRefreshToken);

            $expiresAt = strtotime(SecurityConstants::JWT_EXPIRATION_TIME);
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

            setcookie('access_token', $newAccessToken, $expiresAt, '/', 'localhost', $secure);
            setcookie('refresh_token', $newRefreshToken, $expiresAt, '/', 'localhost', $secure);

            $response = ['accessToken' => $newAccessToken, 'refreshToken' => $newRefreshToken];

            return $this->makeArrayResponse(ResponseCodes::OK, $response);
        } catch (CoreException $exception) {
            $this->log->error(
                sprintf(
                    'Something went wrong trying to verify if the JWT is valid: %s', $exception->getMessage()
                )
            );

            return ["status_code_header" => ResponseCodes::INTERNAL_SERVER_ERROR, "body" => $exception->getMessage()];
        }
    }
}

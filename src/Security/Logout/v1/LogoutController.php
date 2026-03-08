<?php declare(strict_types=1);

namespace Coco\SourceWatcherApi\Security\Logout\v1;

use Coco\SourceWatcherApi\Framework\ApiResponse;
use Coco\SourceWatcherApi\Framework\Controller;
use Coco\SourceWatcherApi\Framework\ResponseCodes;
use Coco\SourceWatcherApi\Security\Constants as SecurityConstants;
use Coco\SourceWatcherApi\Security\JWKS\JWKSHelper;
use Coco\SourceWatcherApi\Security\Refresh\RefreshTokenDAO;
use Exception as CoreException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Invalidates the refresh token server-side so it can no longer be used.
 * Call this on logout so the session is properly ended.
 */
class LogoutController extends Controller
{
    use ApiResponse;

    public function processRequest(string $requestMethod, array $extraOptions): void
    {
        if ($requestMethod !== 'POST') {
            $response = $this->notFoundResponse();
            header($response['status_code_header']);
            if ($response['body']) {
                echo $response['body'];
            }
            return;
        }

        $response = $this->revokeRefreshToken();
        header($response['status_code_header']);

        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function revokeRefreshToken(): array
    {
        $accessToken = $this->requestData['access_token'] ?? null;
        $refreshToken = $this->requestData['refresh_token'] ?? null;

        if (empty($accessToken) || empty($refreshToken)) {
            return $this->makeResponse(ResponseCodes::BAD_REQUEST, 'Missing access_token or refresh_token');
        }

        try {
            $jwksHelper = new JWKSHelper();
            $jwtDecoded = JWT::decode($accessToken, new Key($jwksHelper->getPublicKey(), SecurityConstants::ALGORITHM));
            $userId = (int) $jwtDecoded->data->userId;

            $refreshTokenDao = new RefreshTokenDAO();
            $refreshTokenDao->deleteRefreshToken($userId, $refreshToken);

            $this->clearAuthCookies();

            return $this->makeResponse(ResponseCodes::OK, 'Logged out');
        } catch (CoreException $e) {
            return $this->makeResponse(ResponseCodes::UNAUTHORIZED, 'Invalid token');
        }
    }

    private function clearAuthCookies(): void
    {
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

        setcookie('access_token', '', 1, '/', 'localhost', $secure);
        setcookie('refresh_token', '', 1, '/', 'localhost', $secure);
    }
}

<?php declare(strict_types=1);

namespace Coco\SourceWatcherApi\Security\JWKS;

use Coco\SourceWatcherApi\Security\Constants as SecurityConstants;
use JetBrains\PhpStorm\ArrayShape;

class JWKSHelper
{
    public function getPrivateKey(): string
    {
        return file_get_contents(join('/', [__DIR__, '..', 'keys', 'current', 'private.pem']));
    }

    public function getPublicKey(): string
    {
        return file_get_contents(join('/', [__DIR__, '..', 'keys', 'current', 'public.pem']));
    }

    /**
     * Allows returning the JSON Web Key.
     * @return array
     */
    #[ArrayShape(['kty' => "string", 'e' => "string", 'use' => "string", 'kid' => "string", 'alg' => "string", 'n' => "string"])]
    public function getJWK(): array
    {
        $details = openssl_pkey_get_details(
            openssl_pkey_get_public(
                file_get_contents(
                    join('/', [__DIR__, '..', 'keys', 'current', 'public.pem'])
                )
            )
        );

        $rsa = $details['rsa'];

        return [
            'kty' => 'RSA',
            'e' => base64_encode($rsa['e']),
            'use' => 'sig',
            'kid' => sha1($rsa['n']),
            'alg' => SecurityConstants::ALGORITHM,
            'n' => base64_encode($rsa['n'])
        ];
    }
}

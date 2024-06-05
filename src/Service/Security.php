<?php

namespace App\Service;

use ReallySimpleJWT\Token;

class Security
{
    public static function encodeToken(string $hash, string $issuer = 'default'): string
    {
        $userId = $hash;
        $secret = $_ENV['JWT_TOKEN'];
        $expiration = time() + (60 * 60 * 24); // day

        return Token::create($userId, $secret, $expiration, $issuer);
    }

    /**
     * See https://github.com/RobDWaller/ReallySimpleJWT#parse-and-validate-token
     */
    public static function validateToken(?string $token): bool
    {
        $secret = $_ENV['JWT_TOKEN'];

        return Token::validate($token, $secret);
    }

    /**
     * @throws \Exception
     */
    public static function decodeToken(string $token): array
    {
        return Token::getPayload($token);
    }
}

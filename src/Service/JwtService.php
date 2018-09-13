<?php declare(strict_types=1);

namespace App\Service;

use Firebase\JWT\JWT;

/**
 * Class JwtService
 * @package App\Service
 */
class JwtService implements ServiceInterface
{
    /**
     * @param array $options
     * @param string $key
     * @return string
     */
    public function encode(array $options, string $key): string
    {
        $issuedAt = time();
        $expire = $issuedAt + $options['expiration_sec'];

        $params = [
            'iat'  => $issuedAt,
            'exp'  => $expire,
            'nbf'  => $issuedAt - 1,
            'data' => $options['userdata'],
        ];
        
        return JWT::encode($params, $key);
    }

    /**
     * @param string $token
     * @param string $key
     * @return array
     */
    public function decode(string $token, string $key): array
    {
        try {
            $token = JWT::decode($token, $key, ['HS256']);
        } catch (\Exception $e) {
            return [];
        }
        
        return (array)$token;
    }
}

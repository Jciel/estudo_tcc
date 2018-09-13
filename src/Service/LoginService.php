<?php declare(strict_types=1);

namespace App\Service;

use Ratchet\ConnectionInterface;

/**
 * Class LoginService
 * @package App\Service
 */
class LoginService implements ServiceInterface
{
    /**
     * @var array $loginConfig
     */
    private $loginConfig;
    
    /**
     * @var string $jwtKey
     */
    private $jwtKey;
    
    /**
     * @var JwtService $jwtService
     */
    private $jwtService;

    /**
     * LoginService constructor.
     * @param ServiceInterface $jwtService
     * @param array $config
     */
    public function __construct(ServiceInterface $jwtService, array $config)
    {
        $this->jwtService = $jwtService;
        $this->loginConfig = $config['login'];
        $this->jwtKey = $config['jwtKey'];
    }

    /**
     * @param string $msg
     * @param ConnectionInterface $conn
     * @return array
     */
    public function login(string $msg, ConnectionInterface $conn): array
    {
        $dataLogin = json_decode($msg, true);
        
        if (!array_key_exists($dataLogin['user'], $this->loginConfig)) {
            return [
                'error' => true,
                'message' => 'Usuário não existe',
                'token' => null
            ];
        }
        
        $userLogin = $this->loginConfig[$dataLogin['user']];
        
        if (!($dataLogin['passwd'] === $userLogin['passwd'])) {
            return [
                'error' => true,
                'message' => 'Senha incorreta',
                'token' => null
            ];
        }
        
        $options = [
            'expiration_sec' => 8600,
            'userdata' => [
                'user' => $userLogin['user'],
                'type' => $userLogin['type'],
                'routes' => $userLogin['routes'],
            ]
        ];
        
        $token = $this->jwtService->encode($options, $this->jwtKey);

        return [
            'error' => false,
            'message' => 'Valid',
            'token' => $token
        ];
    }

    /**
     * @param string $token
     * @return array
     */
    public function checkLogin(string $token): array
    {
        $token = $this->jwtService->decode($token, $this->jwtKey);

        $tokenData = array_map(function ($item) {
            return (is_object($item)) ? (array)$item : $item;
        }, $token);
        
        if (empty($tokenData)) {
            return [
                'error' => true,
                'message' => 'Invalid token',
                'token' => null
            ];
        }

        $tokenData['error'] = false;
        return $tokenData;
    }
}

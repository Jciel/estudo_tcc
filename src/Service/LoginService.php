<?php declare(strict_types=1);

namespace App\Service;

use App\ObjectValue\ConnectionLoginData;
use App\ObjectValue\Message;
use App\ObjectValue\MessageInterface;
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
     * @return Message
     */
    public function login(string $msg, ConnectionInterface $conn): Message
    {
        $dataLogin = json_decode($msg, true);

        if (!array_key_exists($dataLogin['user'], $this->loginConfig)) {
            return new Message(true, 'Usuário não existe', null);
        }

        $userLogin = $this->loginConfig[$dataLogin['user']];

        if (!($dataLogin['passwd'] === $userLogin['passwd'])) {
            return new Message(true, 'Usuário não existe', null);
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
        return new Message(false, 'Valid', $token);
    }

    /**
     * @param string $token
     * @return MessageInterface
     */
    public function checkLogin(string $token): MessageInterface
    {
        $token = $this->jwtService->decode($token, $this->jwtKey);

        $tokenData = array_map(function ($item) {
            return (is_object($item)) ? (array)$item : $item;
        }, $token);

        if (empty($tokenData)) {
            return new Message(true, 'Invalid token', null);
        }

        $tokenData['error'] = false;

        return new ConnectionLoginData(
            $tokenData['error'],
            'Conected',
            $tokenData['iat'],
            $tokenData['exp'],
            $tokenData['nbf'],
            $tokenData['data']['user'],
            $tokenData['data']['type'],
            $tokenData['data']['routes'],
            null
        );
    }
}

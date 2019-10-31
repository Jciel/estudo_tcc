<?php declare(strict_types=1);

namespace App\Service;

use App\Command\ConnectionCommand;
use App\Command\ErrorCommand;
use App\Command\Factory\CommandFactory;
use App\Command\Interfaces\CommandInterface;
use App\Command\LogedInCommand;
use App\Service\Interfaces\ServiceInterface;
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
     * @return CommandInterface
     */
    public function login(string $msg, ConnectionInterface $conn): CommandInterface
    {
        $dataLogin = json_decode($msg, true);

        if (!array_key_exists($dataLogin['user'], $this->loginConfig)) {
            return CommandFactory::create(
                ErrorCommand::class,
                ["Usuário não existe", "token"]
            );
        }

        $userLogin = $this->loginConfig[$dataLogin['user']];

        if (!($dataLogin['passwd'] === $userLogin['passwd'])) {
            return CommandFactory::create(
                ErrorCommand::class,
                ["Senha incorreta", "token"]
            );
        }

        $options = [
            'expiration_sec' => 99999999999999,
            'userdata' => [
                'user' => $userLogin['user'],
                'type' => $userLogin['type'],
                'routes' => $userLogin['routes'],
            ]
        ];

        $token = $this->jwtService->encode($options, $this->jwtKey);
        return CommandFactory::create(LogedInCommand::class, [$token]);
    }
    
    /**
     * @param string $token
     * @return CommandInterface
     */
    public function checkLogin(string $token): CommandInterface
    {
        $tokens = $this->jwtService->decode($token, $this->jwtKey);

        $tokenData = array_map(function ($item) {
            return (is_object($item)) ? (array)$item : $item;
        }, $tokens);

        if (empty($tokenData)) {
            return CommandFactory::create(ErrorCommand::class, ["Invalid token", "token"]);
        }
        
        return new ConnectionCommand(
            'Conected',
            $tokenData['iat'],
            $tokenData['exp'],
            $tokenData['nbf'],
            $tokenData['data']['user'],
            $tokenData['data']['type'],
            $tokenData['data']['routes']
        );
    }
}

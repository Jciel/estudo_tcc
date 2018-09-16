<?php declare(strict_types=1);

namespace App\Channel;

use App\Service\LoginService;
use App\Service\ServiceInterface;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

/**
 * Class LoginChannel
 * @package App\Channel
 */
class LoginChannel implements MessageComponentInterface, ChannelInterface
{
    /**
     * @var LoginService $loginService
     */
    private $loginService;

    /**
     * LoginChannel constructor.
     * @param ServiceInterface $loginService
     */
    public function __construct(ServiceInterface $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn): void
    {
        $conn->send(json_encode([
            'error' => false,
            'message' => 'Login opened'
        ]));
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn): void
    {
        $conn->send(json_encode([
            'error' => null,
            'message' => 'Login closed'
        ]));
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        $conn->send(json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]));
        $conn->close();
    }

    /**
     * @param ConnectionInterface $conn
     * @param string $msg
     */
    public function onMessage(ConnectionInterface $conn, $msg): void
    {
        $result = $this->loginService->login($msg, $conn);
        $conn->send(json_encode([
            "error" => $result->isError(),
            "message" => $result->getMessage(),
            "token" => $result->getToken()
        ]));
        $conn->close();
    }
}

<?php declare(strict_types=1);

namespace App\Channel;

use App\ObjectValue\ConnectionLoginData;
use App\ObjectValue\Message;
use App\ObjectValue\MessageInterface;
use App\ObjectValue\TokenDataInterface;
use App\Service\LoginService;
use App\Service\Messages;
use App\Service\MessagesService;
use App\Service\ServiceInterface;
use Closure;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

/**
 * Class ExtruderChannel
 * @package App\Channel
 */
class ExtruderChannel implements MessageComponentInterface, ChannelInterface
{
    use ChannelTrait;

    /**
     * @var ConnectionInterface $extruderConnection
     */
    protected $extruderConnection;

    /**
     * @var LoginService $loginService
     */
    private $loginService;

    /**
     * @var ConnectionInterface $server
     */
    protected $server;
    
    /** @var array $messagesCache */
    private $messagesCache = [];

    /**
     * ExtruderChannel constructor.
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
        $token = $this->getToken($conn->httpRequest);

        /** @var ConnectionLoginData|MessageInterface $tokenData */
        $tokenData = $this->loginService->checkLogin($token);
        
        if ($tokenData->isError()) {
            $conn->send(MessagesService::errorMessage($tokenData));
            $conn->close();
            return;
        }

        if ($tokenData->isServer()) {
            $this->server = $conn;

            array_walk($this->messagesCache, function ($msg) use ($conn) {
                $conn->send($msg);
            });
            $this->messagesCache = [];
        }

        if ($tokenData->isEquipament()) {
            $this->extruderConnection = $conn;
        }
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn): void
    {
        $token = $this->getToken($conn->httpRequest);
        /** @var ConnectionLoginData $tokenData */
        $tokenData = $this->loginService->checkLogin($token);

        echo "Connection closed\n";

        if ($tokenData->isError()) {
            return;
        }

        if ($tokenData->isServer()) {
            $this->server = null;
        }

        if ($tokenData->isEquipament()) {
            $this->extruderConnection = null;
        }
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        echo "Error {$e->getMessage()}\n";
        $conn->close();
    }

    /**
     * @param ConnectionInterface $conn
     * @param string $msg
     */
    public function onMessage(ConnectionInterface $conn, $msg): void
    {
        $token = $this->getToken($conn->httpRequest);
        /** @var MessageInterface|TokenDataInterface $tokenData */
        $tokenData = $this->loginService->checkLogin($token);
        
        $sendMessage = $this->message($tokenData, $msg);

        $sendMessage($conn);
    }
}

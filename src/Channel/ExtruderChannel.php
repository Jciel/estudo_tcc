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
use App\WsClient\WsClient;
use Closure;
use Ratchet\Client\Connector;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use React\Promise\PromiseInterface;
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
     * @var WsClient $clientServer
     */
    private $clientServer;
    
    /** @var array $messagesCache */
    private $messagesCache = [];

    /**
     * ExtruderChannel constructor.
     * @param ServiceInterface $loginService
     */
    public function __construct(ServiceInterface $loginService, WsClient $clientServer)
    {
        $this->loginService = $loginService;
        $this->clientServer = $clientServer;
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
        
        if ($tokenData->notIsEquipment()) {
            echo "não é equipamento valido";
            $conn->close();
            return;
        }

        $this->extruderConnection = $conn;

        /** @var PromiseInterface $connector */
        $connector = $this->clientServer->connect();
        
        var_dump($connector->then());
        exit;
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

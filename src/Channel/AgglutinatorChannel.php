<?php declare(strict_types=1);

namespace App\Channel;

use App\Service\LoginService;
use App\Service\ServiceInterface;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

/**
 * Class AgglutinatorChannel
 * @package App\Channel
 */
class AgglutinatorChannel implements MessageComponentInterface, ChannelInterface
{
    use ChannelTrait;

    /**
     * @var ConnectionInterface $extruderConnection
     */
    protected $agglutinatorConnection;

    /**
     * @var LoginService $loginService
     */
    private $loginService;

    /**
     * @var SplObjectStorage $connections
     */
    protected $clients;


    /**
     * AgglutinatorChannel constructor.
     * @param ServiceInterface $loginService
     */
    public function __construct(ServiceInterface $loginService)
    {
        $this->loginService = $loginService;
        $this->clients = new SplObjectStorage();
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn): void
    {
        $token = $this->getToken($conn->httpRequest);
        $tokenData = $this->loginService->checkLogin($token);

        if ($tokenData['error']) {
            $conn->send(json_encode($tokenData));
            $conn->close();
        }

        if ($tokenData['data']['type'] === 'client') {
            $this->clients->attach($conn);
        }

        if ($tokenData['data']['type'] === 'equipament') {
            $this->extruderConnection = $conn;
        }
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn): void
    {
        echo "Connection closed\n";
        $this->clients->detach($conn);
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
        $tokenData = $this->loginService->checkLogin($token);

        if ($tokenData['data']['type'] === 'client') {
            $this->agglutinatorConnection->send($msg);
        }

        if ($tokenData['data']['type'] === 'equipament') {
            foreach ($this->clients as $client) {
                $client->send($msg);
            }
        }
    }
}

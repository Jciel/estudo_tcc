<?php declare(strict_types=1);

namespace Middleware\WsClient;

use App\Service\ServiceInterface;
use Ratchet\Client\Connector;
use Ratchet\Client\WebSocket;
use Ratchet\RFC6455\Messaging\MessageInterface;
use React\EventLoop\LoopInterface;

class WsClient implements ServiceInterface
{
    private $loop;
    
    
    private $serverChannelsUrls = "ws://localhost:80/extruder?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NDA0ODc1MTgsImV4cCI6MTAwMDAxNTQwNDg3NTE3LCJuYmYiOjE1NDA0ODc1MTcsImRhdGEiOnsidXNlciI6ImpvY2llbCIsInR5cGUiOiJjbGllbnQiLCJyb3V0ZXMiOlsiZXh0cnVkZSJdfX0.oVuBYGwOc5jgZ8SGYvppe1PPk8z-NAMOdMJvkKHHBaE";

    private $raspChannelsUrls = "ws://localhost:8080/extruder?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NDA1MTUxODcsImV4cCI6MTAwMDAxNTQwNTE1MTg2LCJuYmYiOjE1NDA1MTUxODYsImRhdGEiOnsidXNlciI6ImV4dHJ1c29yYSIsInR5cGUiOiJlcXVpcGFtZW50Iiwicm91dGVzIjpbImV4dHJ1ZGUiXX19.jn73drtnHHBl9eFVGGRVI2N6PdhoBEFjGWc2YSuZLlQ";

    /**
     * @var WebSocket[] $connToRasp
     */
    private $connToRasp;

    /**
     * @var WebSocket[] $connToServer
     */
    private $connToServer;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function connect()
    {
        $reactConnector = new \React\Socket\Connector($this->loop, [
            'dns' => '8.8.8.8',
            'timeout' => 10
        ]);

        $connectorToRasp = new Connector($this->loop, $reactConnector);
        $connectorToServer = new Connector($this->loop, $reactConnector);
        
        $connToRasp = null;
        $connToServer = null;

        $connectorToRasp($this->raspChannelsUrls)->then(function (WebSocket $conn) use (&$connToRasp, &$connToServer) {
            $connToRasp = $conn;
            $connToRasp->on('message', function (MessageInterface $msg) use (&$connToServer) {
                $connToServer->send($msg);
            });
        });
        
        $connectorToServer($this->serverChannelsUrls)->then(function (WebSocket $conn) use (&$connToServer, &$connToRasp) {
            $connToServer = $conn;
            $connToServer->on('message', function (MessageInterface $msg) use (&$connToRasp) {
                $connToRasp->send($msg);
            });
        });
    }
}

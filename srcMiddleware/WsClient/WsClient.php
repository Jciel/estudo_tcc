<?php declare(strict_types=1);

namespace App\WsClient;

use Ratchet\Client\Connector;
use Ratchet\Client\WebSocket;
use Ratchet\RFC6455\Messaging\MessageInterface;
use React\EventLoop\LoopInterface;

class WsClient
{
    private $loop;

    /**
     * @var WebSocket $connToRasp
     */
    private $connToRasp;

    /**
     * @var WebSocket $connToServer
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

        $connectorToRasp('ws://127.0.0.1:9000')->then(function (WebSocket $conn) {
            $this->connToRasp = $conn;
        });
        
        $connectorToServer('ws://127.0.0.1:9000')->then(function (WebSocket $conn) {
            $this->connToServer = $conn;
        });
        
        $this->connToServer->on('message', function (MessageInterface $msg) {
            $this->connToRasp->send($msg);
        });

        $this->connToRasp->on('message', function (MessageInterface $msg) {
            $this->connToServer->send($msg);
        });
    }
}

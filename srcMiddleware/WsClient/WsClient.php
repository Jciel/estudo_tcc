<?php declare(strict_types=1);

namespace App\WsClient;

use Ratchet\Client\Connector;
use Ratchet\Client\WebSocket;
use Ratchet\RFC6455\Messaging\MessageInterface;
use React\EventLoop\LoopInterface;

class WsClient
{
    private $loop;
    
    
    private $serverChannelsUrls = [
        "extrusora" => "ws://127.0.0.1:9000"
    ];

    private $raspChannelsUrls = [
        "extrusora" => "ws://localhost:8080/extruder?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NDA1MTUxODcsImV4cCI6MTAwMDAxNTQwNTE1MTg2LCJuYmYiOjE1NDA1MTUxODYsImRhdGEiOnsidXNlciI6ImV4dHJ1c29yYSIsInR5cGUiOiJlcXVpcGFtZW50Iiwicm91dGVzIjpbImV4dHJ1ZGUiXX19.jn73drtnHHBl9eFVGGRVI2N6PdhoBEFjGWc2YSuZLlQ"
    ];

    /**
     * @var WebSocket[] $connToRasp
     */
    private $connToRasp = [];

    /**
     * @var WebSocket[] $connToServer
     */
    private $connToServer = [];

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
        
        foreach ($this->raspChannelsUrls as $name => $raspChannelsUrl) {
            $connectorToRasp($raspChannelsUrl)->then(function (WebSocket $conn) use ($name) {
                $this->connToRasp[$name] = $conn;
            });
        }
        
        foreach ($this->serverChannelsUrls as $name => $serverChannelUrl) {
            $connectorToServer($serverChannelUrl)->then(function (WebSocket $conn) use ($name) {
                $this->connToServer[$name] = $conn;
            });
        }
        
        foreach ($this->connToRasp as $chanel => $connToRasp) {
            $this->connToServer[$chanel]->on('message', function (MessageInterface $msg) use ($connToRasp) {
                $connToRasp->send($msg);
            });
        }
        
        foreach ($this->connToServer as $chanel => $connToServer) {
            $this->connToRasp[$chanel]->on('message', function (MessageInterface $msg) use ($connToServer) {
                $connToServer->send($msg);
            });
        }
    }
}

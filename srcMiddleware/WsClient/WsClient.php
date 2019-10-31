<?php declare(strict_types=1);

namespace Middleware\WsClient;

use App\Service\Interfaces\ServiceInterface;
use Ratchet\Client\Connector;
use Ratchet\Client\WebSocket;
use Ratchet\RFC6455\Messaging\MessageInterface;
use React\EventLoop\LoopInterface;

/**
 * Class WsClient
 * @package Middleware\WsClient
 */
class WsClient implements ServiceInterface
{
    /**
     * @var LoopInterface $loop
     */
    private $loop;

    /**
     * @var string $serverChannelsUrls
     */
    private $serverChannelsUrls = "ws://192.168.254.47:3001";
//    private $serverChannelsUrls = "ws://localhost:80/extruder?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NDA1MTUxODcsImV4cCI6MTAwMDAxNTQwNTE1MTg2LCJuYmYiOjE1NDA1MTUxODYsImRhdGEiOnsidXNlciI6ImV4dHJ1c29yYSIsInR5cGUiOiJlcXVpcGFtZW50Iiwicm91dGVzIjpbImV4dHJ1ZGUiXX19.jn73drtnHHBl9eFVGGRVI2N6PdhoBEFjGWc2YSuZLlQ";

    /**
     * @var string $raspChannelsUrls
     */
    private $raspChannelsUrls = "ws://localhost:8080/extruder?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NDA1MTUxODcsImV4cCI6MTAwMDAxNTQwNTE1MTg2LCJuYmYiOjE1NDA1MTUxODYsImRhdGEiOnsidXNlciI6ImV4dHJ1c29yYSIsInR5cGUiOiJlcXVpcGFtZW50Iiwicm91dGVzIjpbImV4dHJ1ZGUiXX19.jn73drtnHHBl9eFVGGRVI2N6PdhoBEFjGWc2YSuZLlQ";

    /**
     * WsClient constructor.
     * @param LoopInterface $loop
     */
    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function connect(): void
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
        }, function ($e) {
            echo $e->getMessage() .  "\n";
        });
    }
}

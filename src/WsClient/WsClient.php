<?php declare(strict_types=1);

namespace App\WsClient;

use App\Service\ServiceInterface;

use Ratchet\Client\Connector;
use Ratchet\Client\WebSocket;
use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;

class WsClient implements ServiceInterface
{
    private $loop;
    
    private $reactConnector;
    
    public function __construct(LoopInterface $loop, \React\Socket\Connector $reactConnector)
    {
        $this->loop = $loop;
        $this->reactConnector = $reactConnector;
    }
    
    public function connect(): PromiseInterface
    {
        $connector = new Connector($this->loop, $this->reactConnector);
        
        return $connector('ws://127.0.0.1:9000')->then(function (WebSocket $conn){
            return $conn;
        });
    }
}

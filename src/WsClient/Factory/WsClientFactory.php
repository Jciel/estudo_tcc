<?php declare(strict_types=1);

namespace App\WsClient\Factory;

use App\ServiceManagerInterface;
use App\WsClient\WsClient;
use Ratchet\Client\Connector;
use React\EventLoop\Factory;

class WsClientFactory
{
    public function __invoke(ServiceManagerInterface $serviceManager)
    {
        $loop = Factory::create();
        $reactConnector = new \React\Socket\Connector($loop);

//        $connector = new Connector($loop, $reactConnector);

        return new WsClient($loop, $reactConnector);
    }
}

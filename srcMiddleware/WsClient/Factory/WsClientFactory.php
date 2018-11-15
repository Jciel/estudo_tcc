<?php declare(strict_types=1);

namespace App\WsClient\Factory;

use App\ServiceManagerInterface;
use App\WsClient\WsClient;

class WsClientFactory
{
    public function __invoke(ServiceManagerInterface $serviceManager)
    {
        $loop = $serviceManager->getLoop();
        
        return new WsClient($loop);
    }
}

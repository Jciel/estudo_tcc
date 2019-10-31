<?php declare(strict_types=1);

namespace Middleware\WsClient\Factory;

use App\Service\Interfaces\ServiceInterface;
use App\ServiceManagerInterface;
use Middleware\WsClient\WsClient;

class WsClientFactory
{
    public function __invoke(ServiceManagerInterface $serviceManager): ServiceInterface
    {
        $loop = $serviceManager->getLoop();
        
        return new WsClient($loop);
    }
}

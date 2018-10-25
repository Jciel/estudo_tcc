<?php declare(strict_types=1);

namespace App\Service\Factory;

use App\Service\MessagesService;
use App\Service\ServiceInterface;
use App\ServiceManagerInterface;

class MessageServiceFactory
{
    public function __invoke(ServiceManagerInterface $serviceManager): ServiceInterface
    {
        return new MessagesService();
    }
}

<?php declare(strict_types=1);

namespace App\Service\Factory;

use App\Service\Interfaces\ServiceInterface;
use App\Service\ServerMessageService;
use App\ServiceManagerInterface;

/**
 * Class ServerMessageServiceFactory
 * @package App\Service\Factory
 */
class ServerMessageServiceFactory
{
    /**
     * @param ServiceManagerInterface $serviceManager
     * @return ServiceInterface
     */
    public function __invoke(ServiceManagerInterface $serviceManager): ServiceInterface
    {
        return new ServerMessageService();
    }
}

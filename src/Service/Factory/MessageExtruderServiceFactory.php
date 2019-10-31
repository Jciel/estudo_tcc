<?php declare(strict_types=1);

namespace App\Service\Factory;

use App\Service\Interfaces\ServiceInterface;
use App\Service\MessageExtruderService;
use App\ServiceManagerInterface;

/**
 * Class MessageExtruderServiceFactory
 * @package App\Service\Factory
 */
class MessageExtruderServiceFactory
{
    /**
     * @param ServiceManagerInterface $serviceManager
     * @return ServiceInterface
     */
    public function __invoke(ServiceManagerInterface $serviceManager): ServiceInterface
    {
        return new MessageExtruderService();
    }
}

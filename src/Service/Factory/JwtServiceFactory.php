<?php declare(strict_types=1);

namespace App\Service\Factory;

use App\Service\Interfaces\ServiceInterface;
use App\Service\JwtService;
use App\ServiceManagerInterface;

/**
 * Class JwtServiceFactory
 * @package App\Service\Factory
 */
class JwtServiceFactory
{
    /**
     * @param ServiceManagerInterface $serviceManager
     * @return ServiceInterface
     */
    public function __invoke(ServiceManagerInterface $serviceManager): ServiceInterface
    {
        return new JwtService();
    }
}

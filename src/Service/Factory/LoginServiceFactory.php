<?php declare(strict_types=1);

namespace App\Service\Factory;

use App\Service\JwtService;
use App\Service\LoginService;
use App\Service\ServiceInterface;
use App\ServiceManagerInterface;

/**
 * Class LoginServiceFactory
 * @package App\Service\Factory
 */
class LoginServiceFactory
{
    /**
     * @param ServiceManagerInterface $serviceManager
     * @return ServiceInterface
     */
    public function __invoke(ServiceManagerInterface $serviceManager): ServiceInterface
    {
        /** @var JwtService $jwtService */
        $jwtService = $serviceManager->get(JwtService::class);
        
        /** @var array $config */
        $config = $serviceManager->getConfig();
        
        return new LoginService($jwtService, $config);
    }
}

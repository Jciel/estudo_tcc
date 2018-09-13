<?php declare(strict_types=1);

namespace App\Channel\Factory;

use App\Channel\ChannelInterface;
use App\Channel\LoginChannel;
use App\Service\LoginService;
use App\ServiceManagerInterface;

/**
 * Class LoginChannelFactory
 * @package App\Channel\Factory
 */
class LoginChannelFactory
{
    /**
     * @param ServiceManagerInterface $serviceManager
     * @return ChannelInterface
     */
    public function __invoke(ServiceManagerInterface $serviceManager): ChannelInterface
    {
        /** @var LoginService $loginService */
        $loginService = $serviceManager->get(LoginService::class);
        
        return new LoginChannel($loginService);
    }
}

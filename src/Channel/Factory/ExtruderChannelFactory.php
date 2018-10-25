<?php declare(strict_types=1);

namespace App\Channel\Factory;

use App\Channel\ChannelInterface;
use App\Channel\ExtruderChannel;
use App\Service\LoginService;
use App\Service\MessagesService;
use App\ServiceManagerInterface;
use App\WsClient\WsClient;

/**
 * Class ExtruderChannelFactory
 * @package App\Channel\Factory
 */
class ExtruderChannelFactory
{
    /**
     * @param ServiceManagerInterface $serviceManager
     * @return ChannelInterface
     */
    public function __invoke(ServiceManagerInterface $serviceManager): ChannelInterface
    {
        /** @var LoginService $loginService */
        $loginService = $serviceManager->get(LoginService::class);
        
        $messageService = $serviceManager->get(MessagesService::class);
        
        return new ExtruderChannel($loginService, $messageService);
    }
}

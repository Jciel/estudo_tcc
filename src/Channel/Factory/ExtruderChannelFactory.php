<?php declare(strict_types=1);

namespace App\Channel\Factory;

use App\Channel\ExtruderChannel;
use App\Channel\Interfaces\ChannelInterface;
use App\Service\EquipmentMessageInterface;
use App\Service\LoginService;
use App\Service\MessageExtruderService;
use App\Service\ServerMessageInterface;
use App\Service\ServerMessageService;
use App\ServiceManagerInterface;
use React\EventLoop\LoopInterface;

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
        
        /** @var ServerMessageInterface $serverMessageService */
        $serverMessageService = $serviceManager->get(ServerMessageService::class);
        
        /** @var EquipmentMessageInterface $messageExtruderService */
        $messageExtruderService = $serviceManager->get(MessageExtruderService::class);
        
        /** @var LoopInterface $loop */
        $loop = $serviceManager->getLoop();
        
        return new ExtruderChannel($loginService, $serverMessageService, $messageExtruderService, $loop);
    }
}

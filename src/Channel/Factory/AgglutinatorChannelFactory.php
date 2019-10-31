<?php //declare(strict_types=1);
//
//namespace App\Channel\Factory;
//
//use App\Channel\AgglutinatorChannel;
//use App\Channel\ChannelInterface;
//use App\Service\LoginService;
//use App\ServiceManagerInterface;
//
///**
// * Class AgglutinatorChannelFactory
// * @package App\Channel\Factory
// */
//class AgglutinatorChannelFactory
//{
//    /**
//     * @param ServiceManagerInterface $serviceManager
//     * @return ChannelInterface
//     */
//    public function __invoke(ServiceManagerInterface $serviceManager): ChannelInterface
//    {
//        /** @var LoginService $loginService */
//        $loginService = $serviceManager->get(LoginService::class);
//
//        return new AgglutinatorChannel($loginService);
//    }
//}

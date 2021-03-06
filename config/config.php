<?php

return [
    'config' => [
        'login' => require "dataLogin.php",
        'jwtKey' => '621669D70B332319EB7F018342EE3DDFF3474901'
    ],
    
    'services' => [
        \App\Service\LoginService::class => \App\Service\Factory\LoginServiceFactory::class,
        \App\Service\JwtService::class => \App\Service\Factory\JwtServiceFactory::class,
        \App\Service\ServerMessageService::class => \App\Service\Factory\ServerMessageServiceFactory::class,
        \App\Service\MessageExtruderService::class => \App\Service\Factory\MessageExtruderServiceFactory::class,
        
        // WsClient
        \Middleware\WsClient\WsClient::class => \Middleware\WsClient\Factory\WsClientFactory::class
    ],
    'channels' => [
        
        \App\Channel\LoginChannel::class => \App\Channel\Factory\LoginChannelFactory::class,
        \App\Channel\ExtruderChannel::class => \App\Channel\Factory\ExtruderChannelFactory::class,
//        \App\Channel\AgglutinatorChannel::class => \App\Channel\Factory\AgglutinatorChannelFactory::class
    ],
];

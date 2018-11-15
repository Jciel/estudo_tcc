<?php

use App\Channel\ExtruderChannel;
use App\Channel\LoginChannel;
use Ratchet\App;

require_once "vendor/autoload.php";

(function () {
    $loop = \React\EventLoop\Factory::create();
    $app = new App("localhost", 8080, "127.0.0.1", $loop);
    
    /** @var \App\ServiceManager $container */
    $container = require "config/container.php";
    $container->addLoop($loop);
   
    $channels = require "config/channels.php";

    $app->route("/login", $channels->get(LoginChannel::class, $container));
    $app->route("/extruder", $channels->get(ExtruderChannel::class, $container));
//    $app->route("/agglutinator", $channels->get(AgglutinatorChannel::class, $container));

    echo "Starting...\n";
    $app->run();
})();

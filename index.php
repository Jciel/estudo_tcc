<?php

use App\Channel\AgglutinatorChannel;
use App\Channel\ExtruderChannel;
use App\Channel\LoginChannel;
use Ratchet\App;

require_once "vendor/autoload.php";

(function () {
    $app = new App("localhost", 8080);

    $container = require "config/container.php";
    $channels = require "config/channels.php";
    
    $app->route("/login", $channels->get(LoginChannel::class, $container));
    $app->route("/extruder", $channels->get(ExtruderChannel::class, $container));
    $app->route("/agglutinator", $channels->get(AgglutinatorChannel::class, $container));
    
    echo "Starting...\n";
    $app->run();
})();

<?php

$config = require __DIR__ . "/config.php";

$services = $config['services'];
$config = $config['config'];

return new \App\ServiceManager($services, $config);

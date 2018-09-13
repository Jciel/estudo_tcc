<?php

$config = require __DIR__ . "/config.php";

$channels = $config['channels'];

return new \App\ChannelManager($channels);

<?php
/**
 * Created by PhpStorm.
 * User: jciel
 * Date: 12/11/18
 * Time: 20:16
 */

namespace App\Command;

use Ratchet\ConnectionInterface;

interface InitCommandInterface
{
    public function execute(ConnectionInterface $conn, array $actionsCommands);
}

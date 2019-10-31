<?php declare(strict_types=1);

namespace App\Command\Interfaces;

use Ratchet\ConnectionInterface;

/**
 * Interface InitCommandInterface
 * @package App\Command\Interfaces
 */
interface InitCommandInterface
{
    /**
     * @param ConnectionInterface $conn
     * @param array $actionsCommands
     * @return mixed
     */
    public function execute(ConnectionInterface $conn, array $actionsCommands);
}

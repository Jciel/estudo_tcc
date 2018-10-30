<?php declare(strict_types=1);

namespace App\Command;

use Ratchet\ConnectionInterface;

/**
 * Interface CommandInterface
 * @package App\Command
 */
interface CommandInterface
{
    /**
     * @param ConnectionInterface $conn
     * @return mixed
     */
    public function execute(ConnectionInterface $conn);
}

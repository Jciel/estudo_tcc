<?php declare(strict_types=1);

namespace App\Command;

use Ratchet\ConnectionInterface;

interface CommandInterface
{
    public function execute(ConnectionInterface $conn);
}

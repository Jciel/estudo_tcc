<?php declare(strict_types=1);

namespace App\Command;

use App\Command\Interfaces\CommandInterface;
use Ratchet\ConnectionInterface;

/**
 * Class LoginCloseCommand
 * @package App\Command
 */
class LoginCloseCommand implements CommandInterface
{

    /**
     * @param ConnectionInterface $conn
     */
    public function execute(ConnectionInterface $conn): void
    {
        $conn->send(json_encode([
            'error' => null,
            'message' => 'Login closed'
        ]));
    }
}

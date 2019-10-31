<?php declare(strict_types=1);

namespace App\Command;

use App\Command\Interfaces\CommandInterface;
use Ratchet\ConnectionInterface;

/**
 * Class OpenedLogin
 * @package App\Command
 */
class OpenedLogin implements CommandInterface
{
    /**
     * @param ConnectionInterface $conn
     */
    public function execute(ConnectionInterface $conn): void
    {
        $conn->send(json_encode([
            "error" => false,
            "message" => "LoginOpened"
        ]));
    }
}

<?php declare(strict_types=1);

namespace App\Command;

use Ratchet\ConnectionInterface;

class OpenedLogin implements CommandInterface
{
    public function execute(ConnectionInterface $conn)
    {
        $conn->send(json_encode([
            "error" => false,
            "message" => "LoginOpened"
        ]));
    }
}

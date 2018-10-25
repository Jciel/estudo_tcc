<?php declare(strict_types=1);

namespace App\Command;

use Ratchet\ConnectionInterface;

class LogedInCommand implements CommandInterface
{
    /**
     * @var string $token
     */
    private $toekn;
    
    public function __construct(string $token = "")
    {
        $this->toekn = $token;
    }

    public function execute(ConnectionInterface $conn)
    {
        $conn->send(json_encode([
            "error" => false,
            "message" => "Valid",
            "token" => $this->toekn
        ]));
    }
}

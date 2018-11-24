<?php declare(strict_types=1);

namespace App\Command;

use Ratchet\ConnectionInterface;

/**
 * Class LogedInCommand
 * @package App\Command
 */
class LogedInCommand implements CommandInterface, CommandErrorInterface
{
    /**
     * @var string $token
     */
    private $toekn;

    /**
     * LogedInCommand constructor.
     * @param string $token
     */
    public function __construct(string $token = "")
    {
        $this->toekn = $token;
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function execute(ConnectionInterface $conn): void
    {
        $conn->send(json_encode([
            "error" => false,
            "message" => "Valid",
            "token" => $this->toekn
        ]));
    }

    public function isError(): bool
    {
        return false;
    }
}

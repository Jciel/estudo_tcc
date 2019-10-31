<?php declare(strict_types=1);

namespace App\Command;

use App\Command\Interfaces\CommandErrorInterface;
use App\Command\Interfaces\CommandInterface;
use Ratchet\ConnectionInterface;

/**
 * Class ErrorCommand
 * @package App\Command
 */
class ErrorCommand implements CommandErrorInterface, CommandInterface
{
    /**
     * @var bool $error
     */
    private $error = true;

    /**
     * @var string $message
     */
    private $message = "";

    /**
     * @var string|null $token
     */
    private $token = null;

    /**
     * ErrorCommand constructor.
     * @param string $message
     * @param null|string $token
     */
    public function __construct(string $message, ?string $token = null)
    {
        $this->message = $message;
        $this->token = $token;
    }

    /**
     * @param ConnectionInterface $conn
     * @return void
     */
    public function execute(ConnectionInterface $conn): void
    {
        $conn->send(json_encode([
            "error" => $this->error,
            "message" => $this->message,
            "token" => $this->token
        ]));
    }

    public function isError(): bool
    {
        return $this->error;
    }
}

<?php declare(strict_types=1);

namespace App\Command;

use Ratchet\ConnectionInterface;

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
    
    public function __construct(string $message, ?string $token = null)
    {
        $this->message = $message;
        $this->token = $token;
    }

    public function execute(ConnectionInterface $conn)
    {
        $conn->send(json_encode([
            "error" => $this->error,
            "message" => $this->message,
            "token" => $this->token
        ]));
    }
}

<?php declare(strict_types=1);

namespace App\Command;

use Ratchet\ConnectionInterface;

class ConnectionCommand implements CommandInterface, CommandConnectionInterface
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var int
     */
    private $iat;

    /**
     * @var int
     */
    private $exp;

    /**
     * @var int
     */
    private $nbf;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $routes;

    /**
     * @var null|string
     */
    private $token;

    public function __construct(
        string $message,
        int $iat,
        int $exp,
        int $nbf,
        string $user,
        string $type,
        array $routes,
        ?string $token = null
    ) {
        $this->message = $message;
        $this->iat = $iat;
        $this->exp = $exp;
        $this->nbf = $nbf;
        $this->user = $user;
        $this->type = $type;
        $this->routes = $routes;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return null|string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @return int
     */
    public function getIat(): int
    {
        return $this->iat;
    }

    /**
     * @return int
     */
    public function getExp(): int
    {
        return $this->exp;
    }

    /**
     * @return int
     */
    public function getNbf(): int
    {
        return $this->nbf;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @return bool
     */
    public function isServer(): bool
    {
        return ($this->type === 'client');
    }

    /**
     * @return bool
     */
    public function isEquipament(): bool
    {
        return ($this->type === 'equipament');
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function execute(ConnectionInterface $conn)
    {
        // TODO: Implement execute() method.
    }
}

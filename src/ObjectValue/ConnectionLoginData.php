<?php declare(strict_types=1);

namespace App\ObjectValue;

/**
 * Class ConnectionLoginData
 * @package App\ObjectValue
 */
final class ConnectionLoginData implements MessageInterface, TokenDataInterface
{
    /**
     * @var bool
     */
    private $error;

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

    /**
     * ConnectionLoginData constructor.
     * @param bool $error
     * @param string $message
     * @param int $iat
     * @param int $exp
     * @param int $nbf
     * @param string $user
     * @param string $type
     * @param array $routes
     * @param null|string $token
     */
    public function __construct(
        bool $error,
        string $message,
        int $iat,
        int $exp,
        int $nbf,
        string $user,
        string $type,
        array $routes,
        ?string $token
    ) {
        $this->error = $error;
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
     * @return bool
     */
    public function isError(): bool
    {
        return $this->error;
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
    public function notIsEquipment(): bool
    {
        return !($this->type === 'equipament');
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
}

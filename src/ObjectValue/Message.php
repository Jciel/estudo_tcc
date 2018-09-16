<?php declare(strict_types=1);

namespace App\ObjectValue;

/**
 * Class Message
 * @package App\ObjectValue
 */
class Message implements MessageInterface
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
     * @var string
     */
    private $token;

    /**
     * Message constructor.
     * @param bool $error
     * @param string $message
     * @param null|string $token
     */
    public function __construct(bool $error, string $message, ?string $token)
    {
        $this->error = $error;
        $this->message = $message;
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
}

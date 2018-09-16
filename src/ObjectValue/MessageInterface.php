<?php declare(strict_types=1);

namespace App\ObjectValue;

/**
 * Interface MessageInterface
 * @package App\ObjectValue
 */
interface MessageInterface
{
    /**
     * @return bool
     */
    public function isError(): bool;

    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @return null|string
     */
    public function getToken(): ?string;
}

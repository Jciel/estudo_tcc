<?php declare(strict_types=1);

namespace App\Command;

/**
 * Interface CommandConnectionInterface
 * @package App\Command
 */
interface CommandConnectionInterface
{
    /**
     * @return bool
     */
    public function isServer(): bool;

    /**
     * @return bool
     */
    public function isEquipament(): bool;

    /**
     * @return string
     */
    public function getUser(): string;

    /**
     * @return null|string
     */
    public function getToken(): ?string;
}

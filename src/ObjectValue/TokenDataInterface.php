<?php declare(strict_types=1);

namespace App\ObjectValue;

/**
 * Interface TokenDataInterface
 * @package App\ObjectValue
 */
interface TokenDataInterface
{
    /**
     * @return int
     */
    public function getIat(): int;

    /**
     * @return int
     */
    public function getExp(): int;

    /**
     * @return int
     */
    public function getNbf(): int;

    /**
     * @return string
     */
    public function getUser(): string;

    /**
     * @return bool
     */
    public function isServer(): bool;

    /**
     * @return bool
     */
    public function isEquipament(): bool;

    /**
     * @return array
     */
    public function getRoutes(): array;
}

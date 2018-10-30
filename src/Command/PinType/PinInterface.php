<?php declare(strict_types=1);

namespace App\Command\PinType;

/**
 * Interface PinInterface
 * @package App\Command\PinType
 */
interface PinInterface
{
    /**
     * @return int
     */
    public function getPin(): int;

    /**
     * @return string
     */
    public function getStrType(): string;
}

<?php declare(strict_types=1);

namespace App\Command\PinType;

/**
 * Interface DigitalPinInterface
 * @package App\Command\PinType
 */
interface DigitalPinInterface
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

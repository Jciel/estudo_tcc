<?php declare(strict_types=1);

namespace App\Command\PinType;

/**
 * Interface AnalogicPinInterface
 * @package App\Command\PinType
 */
interface AnalogicPinInterface
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

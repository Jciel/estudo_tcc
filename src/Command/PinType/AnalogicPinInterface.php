<?php declare(strict_types=1);

namespace App\Command\PinType;

interface AnalogicPinInterface
{
    public function getPin(): int;

    public function getStrType(): string;
}

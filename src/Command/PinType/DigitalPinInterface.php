<?php declare(strict_types=1);

namespace App\Command\PinType;

interface DigitalPinInterface
{
    public function getPin(): int;

    public function getStrType(): string;
}

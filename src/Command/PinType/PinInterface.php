<?php declare(strict_types=1);

namespace App\Command\PinType;

interface PinInterface
{
    public function getPin(): int;

    public function getStrType(): string;
}

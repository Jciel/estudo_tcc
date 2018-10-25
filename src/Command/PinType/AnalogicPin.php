<?php declare(strict_types=1);

namespace App\Command\PinType;

class AnalogicPin implements PinInterface, AnalogicPinInterface
{
    private $pin;
    
    public function __construct(int $pin)
    {
        $this->pin = $pin;
    }
    
    public function getPin(): int
    {
        return $this->pin;
    }

    public function getStrType(): string
    {
        return 'srla';
    }
}

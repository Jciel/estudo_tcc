<?php declare(strict_types=1);

namespace App\Command\PinType;

class AnalogicPin implements PinInterface, AnalogicPinInterface
{
    private $pin;

    private $function;
    
    public function __construct(int $pin, string $function = 'read')
    {
        $this->pin = $pin;
        $this->function = $function;
    }
    
    public function getPin(): int
    {
        return $this->pin;
    }

    public function getStrType(): string
    {
        return ($this->function === 'write') ? 'ppin' : 'srla';
    }
}

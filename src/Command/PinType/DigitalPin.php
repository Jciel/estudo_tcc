<?php declare(strict_types=1);

namespace App\Command\PinType;

/**
 * Class DigitalPin
 * @package App\Command\PinType
 */
class DigitalPin implements PinInterface, DigitalPinInterface
{
    /**
     * @var int
     */
    private $pin;

    /**
     * @var string
     */
    private $function;

    /**
     * DigitalPin constructor.
     * @param int $pin
     * @param string $function
     */
    public function __construct(int $pin, string $function = 'read')
    {
        $this->pin = $pin;
        $this->function = $function;
    }

    /**
     * @return int
     */
    public function getPin(): int
    {
        return $this->pin;
    }

    /**
     * @return string
     */
    public function getStrType(): string
    {
        return ($this->function === 'write') ? 'ppsw' : 'srld';
    }
}

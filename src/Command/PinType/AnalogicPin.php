<?php declare(strict_types=1);

namespace App\Command\PinType;

use App\Command\PinType\Interfaces\AnalogicPinInterface;
use App\Command\PinType\Interfaces\PinInterface;

/**
 * Class AnalogicPin
 * @package App\Command\PinType
 */
class AnalogicPin implements PinInterface, AnalogicPinInterface
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
     * AnalogicPin constructor.
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
        return ($this->function === 'write') ? 'ppin' : 'srla';
    }
}

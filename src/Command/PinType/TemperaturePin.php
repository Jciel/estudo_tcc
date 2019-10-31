<?php declare(strict_types=1);

namespace App\Command\PinType;

use App\Command\PinType\Interfaces\DigitalPinInterface;
use App\Command\PinType\Interfaces\PinInterface;

/**
 * Class TemperaturePin
 * @package App\Command\PinType
 */
class TemperaturePin implements PinInterface, DigitalPinInterface
{
    /**
     * @var int
     */
    private $pin;

    /**
     * TemperaturePin constructor.
     * @param int $pin
     */
    public function __construct(int $pin)
    {
        $this->pin = $pin;
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
        return 'ptmp';
    }
}

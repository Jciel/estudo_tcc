<?php declare(strict_types=1);

namespace App\Command\PinType\Factory;

use App\Command\PinType\AnalogicPin;
use App\Command\PinType\DigitalPin;
use App\Command\PinType\PinInterface;

class PinFactory
{
    public static function create(string $type, int $pin): PinInterface
    {
        $pinTypes = [
            'digital' => new DigitalPin($pin),
            'analogic' => new AnalogicPin($pin)
        ];
        
        return $pinTypes[$type];
    }
}

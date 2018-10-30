<?php declare(strict_types=1);

namespace App\Command\PinType\Factory;

use App\Command\PinType\AnalogicPin;
use App\Command\PinType\DigitalPin;
use App\Command\PinType\PinInterface;
use App\Command\PinType\TemperaturePin;

class PinFactory
{
    public static function create(string $type, int $pin, string $function = 'read'): PinInterface
    {
        $pinTypes = [
            'digital' => function () use ($pin, $function) {
                return new DigitalPin($pin, $function);
            },
            'analogic' => function () use ($pin, $function) {
                return new AnalogicPin($pin, $function);
            },
            'temp' => function (int $pin) {
                return new TemperaturePin($pin);
            },
        ];
        
        return $pinTypes[$type]($pin);
    }
}

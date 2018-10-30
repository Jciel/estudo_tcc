<?php declare(strict_types=1);

namespace App\Command;

use App\Command\PinType\PinInterface;
use Closure;
use Ratchet\ConnectionInterface;

/**
 * Class EquipamentCommand
 * @package App\Command
 */
class EquipamentCommand implements CommandInterface
{
    /**
     * @var PinInterface
     */
    private $pin;

    /**
     * @var int
     */
    private $value;

    /**
     * @var Closure
     */
    private $reflectionFunction;

    /**
     * EquipamentCommand constructor.
     * @param PinInterface $pin
     * @param int $value
     * @param Closure $reflectionFunction
     */
    public function __construct(PinInterface $pin, int $value, Closure $reflectionFunction)
    {
        $this->pin = $pin;
        $this->value = $value;
        $this->reflectionFunction = $reflectionFunction;
    }

    /**
     * @param ConnectionInterface $conn
     * @return Closure
     */
    public function execute(ConnectionInterface $conn): Closure
    {
        $conn->send(json_encode([
            "pin" => $this->pin->getPin(),
            "value" => $this->value
        ]));
        
        return $this->reflectionFunction;
    }
}

<?php declare(strict_types=1);

namespace App\Command;

use App\Command\Interfaces\CommandInterface;
use App\Command\PinType\Interfaces\PinInterface;
use Closure;
use Ratchet\ConnectionInterface;

/**
 * Class ActionCommand
 * @package App\Command
 */
class ActionCommand implements CommandInterface
{
    /**
     * @var PinInterface
     */
    private $pin;

    /**
     * @var
     */
    private $action;

    /**
     * @var Closure
     */
    private $reflection;

    /**
     * ActionCommand constructor.
     * @param PinInterface $pin
     * @param $action
     * @param Closure $reflection
     */
    public function __construct(PinInterface $pin, $action, Closure $reflection)
    {
        $this->pin = $pin;
        $this->action = $action;
        $this->reflection = $reflection;
    }
    
    /**
     * @param ConnectionInterface $conn
     * @return Closure
     */
    public function execute(ConnectionInterface $conn): Closure
    {
        $type = $this->pin->getStrType();
        $pin = $this->pin->getPin();
        $power = ($this->action === 'HIGH') ? 1 : 0;
        
        if (!empty($this->action)) {
            $conn->send("alp://$type/$pin/$power");
        }
        
        return $this->reflection;
    }
}

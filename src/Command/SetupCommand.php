<?php declare(strict_types=1);

namespace App\Command;

use App\Command\Interfaces\CommandInterface;
use App\Command\PinType\Interfaces\PinInterface;
use Ratchet\ConnectionInterface;

/**
 * Class SetupCommand
 * @package App\Command
 */
class SetupCommand implements CommandInterface
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
     * SetupCommand constructor.
     * @param PinInterface $pin
     * @param $action
     */
    public function __construct(PinInterface $pin, $action)
    {
        $this->pin = $pin;
        $this->action = $action;
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function execute(ConnectionInterface $conn): void
    {
        if ($this->action === "OUTPUT") {
            return;
        }
        
        $type = $this->pin->getStrType();
        $pin = $this->pin->getPin();
        $conn->send("alp://$type/$pin");
    }
}

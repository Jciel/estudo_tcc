<?php declare(strict_types=1);

namespace App\Command;

use App\Command\PinType\DigitalPin;
use App\Command\PinType\PinInterface;
use Ratchet\ConnectionInterface;

class SetupCommand implements CommandInterface
{
    
//    private $commands = [];

    /**
     * @var PinInterface
     */
    private $pin;
    
    private $action;
    
    
    public function __construct(PinInterface $pin, $action)
    {
        $this->pin = $pin;
        $this->action = $action;
    }
    
    public function execute(ConnectionInterface $conn)
    {
        if ($this->action === "OUTPUT") {
            return;
        }
        
        $type = $this->pin->getStrType();
        $pin = $this->pin->getPin();
        $conn->send("alp://$type/$pin");
    }
}

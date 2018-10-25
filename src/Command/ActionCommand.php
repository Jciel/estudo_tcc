<?php declare(strict_types=1);

namespace App\Command;

use App\Command\PinType\PinInterface;
use Closure;
use Ratchet\ConnectionInterface;

class ActionCommand implements CommandInterface
{
    /**
     * @var PinInterface
     */
    private $pin;

    private $action;
    
    private $reflection;
    
    
    public function __construct(PinInterface $pin, $action, $reflection = null)
    {
        $this->pin = $pin;
        $this->action = $action;
        $this->reflection = $reflection;
    }


    public function execute(ConnectionInterface $conn): Closure
    {
        return $this->reflection;
    }
}

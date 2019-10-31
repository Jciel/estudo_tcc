<?php declare(strict_types=1);

namespace App\Command;

use App\Command\Interfaces\CommandInterface;
use Closure;
use Ratchet\ConnectionInterface;

/**
 * Class EmptyCommand
 * @package App\Command
 */
class EmptyCommand implements CommandInterface
{
    /**
     * @var Closure
     */
    private $reflection;

    /**
     * EmptyCommand constructor.
     * @param Closure $reflection
     */
    public function __construct(Closure $reflection)
    {
        $this->reflection = $reflection;
    }

    /**
     * @param ConnectionInterface $conn
     * @return Closure
     */
    public function execute(ConnectionInterface $conn): Closure
    {
        return $this->reflection;
    }
}

<?php declare(strict_types=1);

namespace App\Command;

use App\Command\Interfaces\CommandInterface;
use Closure;
use Ratchet\ConnectionInterface;
use React\EventLoop\LoopInterface;

/**
 * Class EmptyInitCommand
 * @package App\Command
 */
class EmptyInitCommand implements CommandInterface
{
    /**
     * @param ConnectionInterface $conn
     * @param array $actionCommands
     */
    public function executeActionCommands(ConnectionInterface $conn, array $actionCommands): void
    {
    }

    /**
     * @param LoopInterface $loop
     * @param Closure $callback
     */
    public function addTimePeriod(LoopInterface $loop, Closure $callback): void
    {
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function execute(ConnectionInterface $conn): void
    {
    }
}

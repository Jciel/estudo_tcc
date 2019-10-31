<?php declare(strict_types=1);

namespace App\Command;

use App\Command\Interfaces\CommandInterface;
use Closure;
use Ratchet\ConnectionInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;

/**
 * Class InitCommand
 * @package App\Command
 */
class InitCommand implements CommandInterface
{
    /**
     * @var int $intervalTime
     */
    private $intervalTime = 0;

    /**
     * @var int $totalTime
     */
    private $totalTime = 0;

    /**
     * InitCommand constructor.
     * @param int $intervalTime
     * @param int $totalTime
     */
    public function __construct(int $intervalTime, int $totalTime)
    {
        $this->intervalTime = $intervalTime;
        $this->totalTime = $totalTime;
    }

    /**
     * @param ConnectionInterface $conn
     * @param ActionCommand[] $actionCommands
     * @return Closure[] $reflections
     */
    public function executeActionCommands(ConnectionInterface $conn, array $actionCommands): array
    {
        $reflections = array_map(function (ActionCommand $actionCommand) use ($conn): Closure {
            return $actionCommand->execute($conn);
        }, $actionCommands);

        return $reflections;
    }

    /**
     * @param LoopInterface $loop
     * @param Closure $callback
     * @return TimerInterface
     */
    public function addTimePeriod(LoopInterface $loop, Closure $callback): TimerInterface
    {
        return $loop->addPeriodicTimer($this->intervalTime, $callback);
    }

    public function addEndTime(LoopInterface $loop, Closure $callback): void
    {
        $loop->addTimer($this->totalTime, $callback);
    }

    /**
     * @param ConnectionInterface $conn
     * @return mixed
     */
    public function execute(ConnectionInterface $conn)
    {
        // TODO: Implement execute() method.
    }
}

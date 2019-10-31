<?php declare(strict_types=1);

namespace App\Service\Interfaces;

use App\Command\Interfaces\CommandInterface;

/**
 * Interface ServerMessageInterface
 * @package App\Service
 */
interface ServerMessageInterface
{
    /**
     * @param string $msg
     * @return CommandInterface[]
     */
    public function parseServerSetupMessage(string $msg): array;

    /**
     * @param string $msg
     * @return CommandInterface[]
     */
    public function parseServerActionMessage(string $msg): array;

    /**
     * @param string $msg
     * @return CommandInterface
     */
    public function parseServerInitMessage(string $msg): CommandInterface;
}

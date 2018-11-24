<?php declare(strict_types=1);

namespace App\Service;

use App\Command\CommandInterface;

/**
 * Interface EquipmentMessageInterface
 * @package App\Service
 */
interface EquipmentMessageInterface
{
    /**
     * @param string $msg
     * @return CommandInterface
     */
    public function parseEquipmentMessage(string $msg): CommandInterface;
}

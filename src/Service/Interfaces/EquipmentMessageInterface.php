<?php declare(strict_types=1);

namespace App\Service\Interfaces;

use App\Command\Interfaces\CommandInterface;

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

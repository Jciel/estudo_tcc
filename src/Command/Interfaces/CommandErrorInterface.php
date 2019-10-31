<?php declare(strict_types=1);

namespace App\Command\Interfaces;

/**
 * Interface CommandErrorInterface
 * @package App\Command
 */
interface CommandErrorInterface
{
    public function isError(): bool;
}

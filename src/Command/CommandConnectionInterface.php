<?php declare(strict_types=1);

namespace App\Command;

interface CommandConnectionInterface
{
    public function isServer(): bool;
    
    public function isEquipament(): bool;

    public function getUser(): string;

    public function getToken(): ?string;
}

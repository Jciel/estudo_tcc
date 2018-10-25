<?php declare(strict_types=1);

namespace App\Command\Factory;

use App\Command\CommandInterface;
use App\Command\ErrorCommand;
use App\Command\LogedInCommand;
use App\Command\OpenedLogin;

class CommandFactory
{
    public static function create(string $type, array $commandsArgs = []): CommandInterface
    {
        extract($commandsArgs);
        
        $commands = [
            OpenedLogin::class => new OpenedLogin(),
            LogedInCommand::class => new LogedInCommand($token ?? ""),
            ErrorCommand::class => new ErrorCommand($message ?? "", $token ?? "")
        ];
        
        return $commands[$type];
    }
}
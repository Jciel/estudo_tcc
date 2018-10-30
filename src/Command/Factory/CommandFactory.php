<?php declare(strict_types=1);

namespace App\Command\Factory;

use App\Command\CommandInterface;
use App\Command\ErrorCommand;
use App\Command\LogedInCommand;
use App\Command\LoginCloseCommand;
use App\Command\OpenedLogin;

/**
 * Class CommandFactory
 * @package App\Command\Factory
 */
class CommandFactory
{
    /**
     * @param string $type
     * @param array $commandsArgs
     * @return CommandInterface
     */
    public static function create(string $type, array $commandsArgs = []): CommandInterface
    {
        extract($commandsArgs);
        
        $commands = [
            OpenedLogin::class => new OpenedLogin(),
            LogedInCommand::class => new LogedInCommand($token ?? ""),
            ErrorCommand::class => new ErrorCommand($message ?? "", $token ?? ""),
            LoginCloseCommand::class => new LoginCloseCommand(),
        ];
        
        return $commands[$type];
    }
}

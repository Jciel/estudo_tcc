<?php declare(strict_types=1);

namespace App\Command\Factory;

use App\Command\ActionCommand;
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
//        extract($commandsArgs);
        
        $commands = [
            OpenedLogin::class => function () {
                return new OpenedLogin();
            },
            LogedInCommand::class => function ($token) {
                return new LogedInCommand($token);
            },
            ErrorCommand::class => function ($message, $token) {
                return new ErrorCommand($message, $token);
            },
            LoginCloseCommand::class => function () {
                return new LoginCloseCommand();
            },
            ActionCommand::class => function ($pin, $action, $reflection) {
                return new ActionCommand($pin, $action, $reflection);
            }
        ];
        
        return $commands[$type](...$commandsArgs);
    }
}

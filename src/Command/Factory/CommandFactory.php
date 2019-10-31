<?php declare(strict_types=1);

namespace App\Command\Factory;

use App\Command\ActionCommand;
use App\Command\EmptyCommand;
use App\Command\EmptyInitCommand;
use App\Command\ErrorCommand;
use App\Command\InitCommand;
use App\Command\Interfaces\CommandInterface;
use App\Command\LogedInCommand;
use App\Command\LoginCloseCommand;
use App\Command\OpenedLogin;
use App\Command\SetupCommand;
use Closure;

/**
 * Class CommandFactory
 * @package App\Command\Factory
 */
class CommandFactory
{
    const ANONIMOUS_INIT_COMMAND = "AnonymousInitCommand";
    const ANONIMOUS_COMMAND = "AnonymousCommand";
    
    /**
     * @param string $type
     * @param array $commandsArgs
     * @return CommandInterface
     */
    public static function create(string $type, array $commandsArgs = []): CommandInterface
    {
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
            },
            InitCommand::class => function ($timeInterval, $totalTime) {
                return new InitCommand($timeInterval, $totalTime);
            },
            SetupCommand::class => function ($pin, $action) {
                return new SetupCommand($pin, $action);
            },
            self::ANONIMOUS_INIT_COMMAND => function () {
                return new EmptyInitCommand();
            },
            self::ANONIMOUS_COMMAND => function (Closure $reflection) {
                return new EmptyCommand($reflection);
            }
        ];
        
        return $commands[$type](...$commandsArgs);
    }
}

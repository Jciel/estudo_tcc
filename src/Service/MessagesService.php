<?php declare(strict_types=1);

namespace App\Service;

use App\Command\ActionCommand;
use App\Command\CommandInterface;
use App\Command\EquipamentCommand;
use App\Command\Factory\CommandFactory;
use App\Command\InitCommand;
use App\Command\PinType\Factory\PinFactory;
use App\Command\PinType\PinInterface;
use App\Command\SetupCommand;
use App\ObjectValue\MessageInterface;
use Ratchet\ConnectionInterface;

/**
 * Class MessagesService
 * @package App\Service
 */
class MessagesService implements ServiceInterface
{
    /**
     * @param string $msg
     * @return CommandInterface[]
     */
    public function parseServerMessage(string $msg): array
    {
        $msgArray = json_decode($msg, true);
        
        $commands = [];
        
        if (array_key_exists('setup', $msgArray)) {
            $setupCommands = array_map(function ($setupCommand) {
                $pin = PinFactory::create($setupCommand['tipo'], $setupCommand['pino']);
                if ($setupCommand['acao'] === 'TEMP') {
                    $pin = PinFactory::create('temp', $setupCommand['pino']);
                }
                return new SetupCommand($pin, $setupCommand['acao']);
            }, $msgArray['setup']);
            
            $commands['setupCommands'] = $setupCommands;
        }

        if (array_key_exists('acoes', $msgArray)) {
            $actionsCommands = array_map(function ($actionCommand) {
                $pin = PinFactory::create($actionCommand['tipo'], $actionCommand['pino'], 'write');
                return $this->createActionCommand($actionCommand, $pin);
            }, $msgArray['acoes']);
            
            $commands['actionCommands'] = $actionsCommands;
        }
        
        if (array_key_exists('inicio', $msgArray)) {
            $initCommand = $msgArray['inicio'];
            $commands['initCommand'] = new InitCommand($initCommand['tempointervalo'], $initCommand['tempototal']);
        }
        
        return $commands;
    }

    /**
     * @param string $msg
     * @return CommandInterface
     */
    public function parseEquipamentMessage(string $msg): CommandInterface
    {
        $equipamentMessageArray = explode('/', preg_replace("/[\/]+/", '/', $msg));
        
        $pin = (int)$equipamentMessageArray[2];
        $value = (int)$equipamentMessageArray[3];
        
        return new EquipamentCommand(
            PinFactory::create('digital', $pin, 'write'),
            $value,
            function ($reflections) use ($pin, $value): CommandInterface {
                if (!array_key_exists($pin, $reflections) || $value === $reflections[$pin]['action']) {
                    return new class implements CommandInterface {
                        public function execute(ConnectionInterface $conn)
                        {
                        }
                    };
                }
                
                if ($value > $reflections[$pin]['action']) {
                    return new ActionCommand(
                        $reflections[$pin]['pin'],
                        $reflections[$pin]['baixo'],
                        function () {
                        }
                    );
                }
                
                if ($value < $reflections[$pin]['action']) {
                    return new ActionCommand(
                        $reflections[$pin]['pin'],
                        $reflections[$pin]['alto'],
                        function () {
                        }
                    );
                }
            }
        );
    }
    
    private function createActionCommand(array $actionCommand, PinInterface $pin): CommandInterface
    {
        if ($this->notHasReflection($actionCommand)) {
            return CommandFactory::create(ActionCommand::class, [
                $pin,
                $actionCommand['acao'],
                function (array &$reflections): void {
                }
            ]);
        }
        
        return CommandFactory::create(ActionCommand::class, [
            $pin,
            $actionCommand['acao'],
            function (array &$reflections) use ($actionCommand): void {
                $reflections[$actionCommand['reflexo']['pino']] = [
                    'pin' => PinFactory::create('digital', (int)$actionCommand['pino'], 'write'),
                    'action' => $actionCommand['acao'],
                    'alto' => 'HIGH',
                    'baixo' => 'LOW'
                ];
            }
        ]);
    }
    
    private function notHasReflection($actionCommand)
    {
        return empty($actionCommand['reflexo']);
    }
}

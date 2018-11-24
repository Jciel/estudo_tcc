<?php declare(strict_types=1);

namespace App\Service;

use App\Command\ActionCommand;
use App\Command\CommandInterface;
use App\Command\Factory\CommandFactory;
use App\Command\InitCommand;
use App\Command\PinType\Factory\PinFactory;
use App\Command\PinType\PinInterface;
use App\Command\SetupCommand;

/**
 * Class ServerMessageService
 * @package App\Service
 */
class ServerMessageService implements ServerMessageInterface, ServiceInterface
{
    /**
     * @param string $msg
     * @return CommandInterface[]
     */
    public function parseServerSetupMessage(string $msg): array
    {
        $msgArray = json_decode($msg, true);
        
        if (!array_key_exists('setup', $msgArray)) {
            return [];
        }
        
        return array_map(function ($setupCommand) {
            $pin = PinFactory::create($setupCommand['tipo'], $setupCommand['pino']);
            if ($setupCommand['acao'] === 'TEMP') {
                $pin = PinFactory::create('temp', $setupCommand['pino']);
            }
            return CommandFactory::create(SetupCommand::class, [$pin, $setupCommand['acao']]);
        }, $msgArray['setup']);
    }

    /**
     * @param string $msg
     * @return ActionCommand[]
     */
    public function parseServerActionMessage(string $msg): array
    {
        $msgArray = json_decode($msg, true);

        if (!array_key_exists('acoes', $msgArray)) {
            return [];
        }
        
        return array_map(function ($actionCommand) {
            $pin = PinFactory::create($actionCommand['tipo'], $actionCommand['pino'], 'write');
            return $this->createActionCommand($actionCommand, $pin);
        }, $msgArray['acoes']);
    }

    /**
     * @param string $msg
     * @return CommandInterface
     */
    public function parseServerInitMessage(string $msg): CommandInterface
    {
        $msgArray = json_decode($msg, true);

        if (!array_key_exists('inicio', $msgArray)) {
            return CommandFactory::create(CommandFactory::ANONIMOUS_INIT_COMMAND, []);
        }
        
        $initCommand = $msgArray['inicio'];
        $timeInterval = $initCommand['tempointervalo'];
        $totalTime = $initCommand['tempototal'];
        return CommandFactory::create(InitCommand::class, [$timeInterval, $totalTime]);
    }
    
    /**
     * @param array $actionCommand
     * @param PinInterface $pin
     * @return CommandInterface
     */
    private function createActionCommand(array $actionCommand, PinInterface $pin): CommandInterface
    {
        $index = ($this->notHasReflection($actionCommand)) ? $actionCommand['pino'] : $actionCommand['reflexo']['pino'];
        $action = ($this->notHasReflection($actionCommand)) ? null : $actionCommand['acao'];
        return CommandFactory::create(ActionCommand::class, [
            $pin,
            $actionCommand['acao'],
            function (array &$reflections) use ($actionCommand, $index, $action): void {
                $reflections[$index] = [
                    'pin' => PinFactory::create('digital', (int)$actionCommand['pino'], 'write'),
                    'action' => $action,
                    'alto' => 'HIGH',
                    'baixo' => 'LOW'
                ];
            }
        ]);
    }
    
    /**
     * @param array $actionCommand
     * @return bool
     */
    private function notHasReflection(array $actionCommand): bool
    {
        return empty($actionCommand['reflexo']);
    }
}

<?php declare(strict_types=1);

namespace App\Service;

use App\Command\ActionCommand;
use App\Command\CommandInterface;
use App\Command\EquipamentCommand;
use App\Command\PinType\Factory\PinFactory;
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
        
        $setupCommands = [];
        if (array_key_exists('setup', $msgArray)) {
            $setupCommands = array_map(function ($setupCommand) {
                
                $pin = PinFactory::create($setupCommand['tipo'], $setupCommand['pino']);

                if ($setupCommand['acao'] === 'TEMP') {
                    $pin = PinFactory::create('temp', $setupCommand['pino']);
                }
                
                return new SetupCommand($pin, $setupCommand['acao']);
            }, $msgArray['setup']);
        }
        $commands['setupCommands'] = $setupCommands;

        $actionsCommands = [];
        if (array_key_exists('acoes', $msgArray)) {
            $actionsCommands = array_map(function ($actionCommand) {
                $pin = PinFactory::create($actionCommand['tipo'], $actionCommand['pino'], 'write');

                return (empty($actionCommand['reflexo']))
                    ? new ActionCommand(
                        $pin,
                        $actionCommand['acao'],
                        function (array &$reflections) {
                        }
                    )
                    : new ActionCommand(
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
                    );
            }, $msgArray['acoes']);
        }
        $commands['actionCommands'] = $actionsCommands;
        
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
                if (!array_key_exists($pin, $reflections)) {
                    return new class implements CommandInterface {
                        public function execute(ConnectionInterface $conn)
                        {
                        }
                    };
                }
                
                if ($value > $reflections[$pin]['action']) {
                    return new ActionCommand($reflections[$pin]['pin'], $reflections[$pin]['baixo']);
                }
                
                if ($value < $reflections[$pin]['action']) {
                    return new ActionCommand($reflections[$pin]['pin'], $reflections[$pin]['alto']);
                }
            }
        );
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    


//    /**
//     * @param MessageInterface $tokenData
//     * @return string
//     */
//    public static function errorMessage(MessageInterface $tokenData): string
//    {
//        return json_encode([
//            'error' => $tokenData->isError(),
//            'message' => $tokenData->getMessage(),
//            'token' => $tokenData->getToken()
//        ]);
//    }
//
//    /**
//     * @return string
//     */
//    public static function equipamentDisconected(): string
//    {
//        return '{"error": true, "message": "Equipament disconected", "token": null}';
//    }
//
//    /**
//     * @return string
//     */
//    public static function serverDiconected()
//    {
//        return '{"error": true, "message": "Server disconected", "token": null}';
//    }
//
//    /**
//     * @param string $msg
//     * @return string
//     */
//    public static function message(string $msg): string
//    {
//        return json_encode([
//            'error' => false,
//            'message' => $msg,
//            'token' => null
//        ]);
//    }
//
//    /**
//     * @return string
//     */
//    public static function loginOpened(): string
//    {
//        return json_encode([
//            'error' => false,
//            'message' => 'Login opened'
//        ]);
//    }
//
//    /**
//     * @return string
//     */
//    public static function loginClose(): string
//    {
//        return json_encode([
//            'error' => null,
//            'message' => 'Login closed'
//        ]);
//    }
//
//    /**
//     * @param \Exception $e
//     * @return string
//     */
//    public static function loginError(\Exception $e): string
//    {
//        return json_encode([
//            'error' => true,
//            'message' => $e->getMessage()
//        ]);
//    }

}

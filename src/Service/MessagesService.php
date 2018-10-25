<?php declare(strict_types=1);

namespace App\Service;

use App\Command\ActionCommand;
use App\Command\PinType\AnalogicPin;
use App\Command\PinType\DigitalPin;
use App\Command\PinType\Factory\PinFactory;
use App\Command\SetupCommand;
use App\ObjectValue\MessageInterface;
use Ratchet\ConnectionInterface;

/**
 * Class Messages
 * @package App\Service
 */
class MessagesService implements ServiceInterface
{
    public function isDigitalPin(string $type): bool
    {
        return ($type == 'digital');
    }

    public function parseMessage(string $msg): array
    {
        $msgArray = json_decode($msg, true);
        $commands = [];
        $setupCommands = [];

        if (array_key_exists('setup', $msgArray)) {
            $setupCommands = array_map(function ($setupCommand) {
                $pin = $this->isDigitalPin($setupCommand['type'])
                    ? new DigitalPin($setupCommand['pino'])
                    : new AnalogicPin($setupCommand['pino']);

                return new SetupCommand($pin, $setupCommand['acao']);
            }, $msgArray['setup']);
        }
        
        $commands['setupCommands'] = $setupCommands;

        
        if (array_key_exists('acoes', $msgArray)) {
            
            $actionsCommands = array_map(function ($actionCommand) {
                $pin = PinFactory::create($actionCommand['type'], $actionCommand['pino']);
                
                
                if (empty($actionCommand['reflexo'])) {
                    return new ActionCommand($pin, $actionCommand['acao']);
                }
                
                $reflection = function (array &$reflections) use ($actionCommand) {
                    $reflections[$actionCommand['reflexo']['pino']] = [
                        'action' => $actionCommand['acao'],
                        'alto' => 'HIGH',
                        'baixo' => 'LOW'
                    ];
                };
                
                return new ActionCommand($pin, $actionCommand['acao'], $reflection);
            }, $msgArray['acoes']);
        }


        $commands['actionCommands'] = $actionsCommands;


        return $commands;
        
//        var_dump($msgArray);
//        var_dump($msg);
//        var_dump($commands);
//        exit;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    


    /**
     * @param MessageInterface $tokenData
     * @return string
     */
    public static function errorMessage(MessageInterface $tokenData): string
    {
        return json_encode([
            'error' => $tokenData->isError(),
            'message' => $tokenData->getMessage(),
            'token' => $tokenData->getToken()
        ]);
    }

    /**
     * @return string
     */
    public static function equipamentDisconected(): string
    {
        return '{"error": true, "message": "Equipament disconected", "token": null}';
    }

    /**
     * @return string
     */
    public static function serverDiconected()
    {
        return '{"error": true, "message": "Server disconected", "token": null}';
    }

    /**
     * @param string $msg
     * @return string
     */
    public static function message(string $msg): string
    {
        return json_encode([
            'error' => false,
            'message' => $msg,
            'token' => null
        ]);
    }

    public static function loginOpened(): string
    {
        return json_encode([
            'error' => false,
            'message' => 'Login opened'
        ]);
    }

    public static function loginClose(): string
    {
        return json_encode([
            'error' => null,
            'message' => 'Login closed'
        ]);
    }

    public static function loginError(\Exception $e): string
    {
        return json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]);
    }
}

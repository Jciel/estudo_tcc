<?php declare(strict_types=1);

namespace App\Service;

use App\Command\ActionCommand;
use App\Command\EquipamentCommand;
use App\Command\Factory\CommandFactory;
use App\Command\Interfaces\CommandInterface;
use App\Command\PinType\Factory\PinFactory;
use App\Service\Interfaces\EquipmentMessageInterface;
use App\Service\Interfaces\ServiceInterface;
use Ratchet\ConnectionInterface;

class MessageExtruderService implements EquipmentMessageInterface, ServiceInterface
{
    /**
     * @param string $msg
     * @return CommandInterface
     */
    public function parseEquipmentMessage(string $msg): CommandInterface
    {
        $equipamentMessageArray = explode('/', preg_replace("/[\/]+/", '/', $msg));

        $pin = (int)$equipamentMessageArray[2];
        $value = (int)$equipamentMessageArray[3];

        return new EquipamentCommand(
            PinFactory::create('digital', $pin, 'write'),
            $value,
            function ($reflections) use ($pin, $value): CommandInterface {
                $reflectionInfo = $reflections[$pin];
                $action = ($value > $reflectionInfo['action']) ? $reflectionInfo['baixo'] : $reflectionInfo['alto'];
                $action = (empty($reflectionInfo['action'])) ? null : $action;

                return $this->actionCommand($reflections, $pin, $value, $action);
            }
        );
    }
    
    private function actionCommand($reflections, $pin, $value, $action): CommandInterface
    {
        return CommandFactory::create(ActionCommand::class, [
            $reflections[$pin]['pin'],
            $action,
            function (ConnectionInterface $serverConnection) use ($pin, $value): void {
                $serverConnection->send(json_encode([
                    "pin" => $pin,
                    "value" => $value
                ]));
            }
        ]);
    }
}

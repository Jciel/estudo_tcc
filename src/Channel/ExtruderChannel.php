<?php declare(strict_types=1);

namespace App\Channel;

use App\Channel\Interfaces\ChannelInterface;
use App\Command\ActionCommand;
use App\Command\EquipamentCommand;
use App\Command\ErrorCommand;
use App\Command\InitCommand;
use App\Command\Interfaces\CommandConnectionInterface;
use App\Command\Interfaces\CommandErrorInterface;
use App\Command\Interfaces\CommandInterface;
use App\Command\SetupCommand;
use App\Service\Interfaces\EquipmentMessageInterface;
use App\Service\Interfaces\ServerMessageInterface;
use App\Service\LoginService;
use Closure;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use React\EventLoop\LoopInterface;

/**
 * Class ExtruderChannel
 * @package App\Channel
 */
class ExtruderChannel implements MessageComponentInterface, ChannelInterface
{
    use ChannelTrait;

    /**
     * @var ?ConnectionInterface $extruderConnection
     */
    protected $extruderConnection;

    /**
     * @var ?ConnectionInterface $clientServer
     */
    private $clientServer;
    
    /**
     * @var LoginService $loginService
     */
    private $loginService;
    
    /**
     * @var ServerMessageInterface $serverMessageService
     */
    private $serverMessageService;
    
    /** @var EquipmentMessageInterface $messageExtruderService */
    private $messageExtruderService;

    /**
     * @var array $reflections
     */
    private $reflections = [];

    /**
     * @var Closure[] $actionCommands
     */
    private $actionCommands = [];
    
    /**
     * @var Closure[] $sendMessageToServerFunctions
     */
    private $sendMessageToServerFunctions = [];

    /**
     * @var LoopInterface $loop
     */
    private $loop;

    /**
     * ExtruderChannel constructor.
     * @param LoginService $loginService
     * @param ServerMessageInterface $serverMessageService
     * @param EquipmentMessageInterface $messageExtruderService
     * @param LoopInterface $loop
     */
    public function __construct(
        LoginService $loginService,
        ServerMessageInterface $serverMessageService,
        EquipmentMessageInterface $messageExtruderService,
        LoopInterface $loop
    ) {
        $this->loginService = $loginService;
        $this->serverMessageService = $serverMessageService;
        $this->messageExtruderService = $messageExtruderService;
        $this->loop = $loop;
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn): void
    {
        $token = $this->getToken($conn->httpRequest);

        /** @var CommandErrorInterface|CommandConnectionInterface|CommandInterface $tokenData */
        $tokenData = $this->loginService->checkLogin($token);
        
        if ($tokenData->isError()) {
            $tokenData->execute($conn);
            $conn->close();
            return;
        }
        
        if ($tokenData->isServer()) {
            $this->clientServer = $conn;
        }
        
        if ($tokenData->isEquipament()) {
            $this->extruderConnection = $conn;
        }
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn): void
    {
        $token = $this->getToken($conn->httpRequest);

        /** @var CommandErrorInterface|CommandConnectionInterface $tokenData */
        $tokenData = $this->loginService->checkLogin($token);

        echo "Connection closed\n";

        if ($tokenData instanceof ErrorCommand) {
            return;
        }

        if ($tokenData->isServer()) {
            $this->clientServer = null;
        }

        if ($tokenData->isEquipament()) {
            $this->extruderConnection = null;
        }
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        echo "Error {$e->getMessage()}\n";
        $conn->close();
    }

    /**
     * @param ConnectionInterface $conn
     * @param string $msg
     */
    public function onMessage(ConnectionInterface $conn, $msg): void
    {
        $token = $this->getToken($conn->httpRequest);

        /** @var CommandErrorInterface|CommandConnectionInterface|CommandInterface $checkLoginCommand */
        $checkLoginCommand = $this->loginService->checkLogin($token);

        if ($checkLoginCommand->isError()) {
            $checkLoginCommand->execute($conn);
            return;
        }

        if ($checkLoginCommand->isServer()) {
            $this->serverMessage($msg);
            return;
        }

        if ($checkLoginCommand->isEquipament()) {
            /** @var EquipamentCommand $equipamentCommand */
            $equipamentCommand = $this->messageExtruderService->parseEquipmentMessage($msg);

            /** @var Closure $equipamentReflectionFunction */
            $equipamentReflectionFunction = $equipamentCommand->execute($this->clientServer);


            /** @var ActionCommand $actionCommandReflection */
            $actionCommandReflection = $equipamentReflectionFunction($this->reflections);
            $sendMessageToServerFunction = $actionCommandReflection->execute($this->extruderConnection);
            if (!empty($sendMessageToServerFunction)) {
                $pin = $equipamentCommand->getPin()->getPin();
                $this->sendMessageToServerFunctions[$pin] = $sendMessageToServerFunction;
            }
        }
    }
    
    private function serverMessage(string $msg): void
    {
        $setupCommands = $this->serverMessageService->parseServerSetupMessage($msg);

        array_walk($setupCommands, function ($setupCommand) {
            /** @var SetupCommand $setupCommand */
            $setupCommand->execute($this->extruderConnection);
        });
        
        /** @var CommandInterface[] $actionCommands */
        $actionCommands = $this->serverMessageService->parseServerActionMessage($msg);
        if (!empty($actionCommands)) {
            $this->actionCommands = $actionCommands;
        }

        /** @var InitCommand $initCommand */
        $initCommand = $this->serverMessageService->parseServerInitMessage($msg);
        /** @var Closure[] $addReflectionFunctions */
        $addReflectionFunctions = $initCommand->executeActionCommands($this->extruderConnection, $this->actionCommands);

        if (empty($addReflectionFunctions)) {
            return;
        }
        
        array_walk($addReflectionFunctions, function (Closure $addReflection): void {
            $addReflection($this->reflections);
        });
        
        $timePeriod = $initCommand->addTimePeriod($this->loop, function () {
            array_walk($this->sendMessageToServerFunctions, function (Closure $sendMessageToServer): void {
                $sendMessageToServer($this->clientServer);
            });
        });
        
        $initCommand->addEndTime($this->loop, function () use ($timePeriod) {
            $this->loop->cancelTimer($timePeriod);
            echo "timer cancelado";
        });
    }
}

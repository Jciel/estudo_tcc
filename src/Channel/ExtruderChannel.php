<?php declare(strict_types=1);

namespace App\Channel;

use App\Command\ActionCommand;
use App\Command\CommandConnectionInterface;
use App\Command\CommandErrorInterface;
use App\Command\CommandInterface;
use App\Command\EquipamentCommand;
use App\Command\ErrorCommand;
use App\Command\InitCommand;
use App\Command\SetupCommand;
use App\Service\LoginService;
use App\Service\MessagesService;
use App\Service\ServiceInterface;
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
     * @var ConnectionInterface $extruderConnection
     */
    protected $extruderConnection;

    /**
     * @var ConnectionInterface $clientServer
     */
    private $clientServer;
    
    /**
     * @var LoginService $loginService
     */
    private $loginService;
    
    /**
     * @var MessagesService $messageService
     */
    private $messageService;

    /**
     * @var array $reflections
     */
    private $reflections = [];

    /**
     * @var Closure[] $actionCommands
     */
    private $actionCommands = [];
    
    /**
     * @var Closure[]
     */
    private $sendMessageToServerFunctions = [];

    /**
     * @var LoopInterface
     */
    private $loop;
    
    /**
     * ExtruderChannel constructor.
     * @param ServiceInterface $loginService
     * @param MessagesService $messageService
     * @param LoopInterface $loop
     */
    public function __construct(ServiceInterface $loginService, MessagesService $messageService, LoopInterface $loop)
    {
        $this->loginService = $loginService;
        $this->messageService = $messageService;
        $this->loop = $loop;
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn): void
    {
        $token = $this->getToken($conn->httpRequest);

        /** @var CommandErrorInterface|CommandConnectionInterface $tokenData */
        $tokenData = $this->loginService->checkLogin($token);
        
        if ($tokenData instanceof ErrorCommand) {
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
            $this->server = null;
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

        /** @var CommandErrorInterface|CommandConnectionInterface $checkLoginCommand */
        $checkLoginCommand = $this->loginService->checkLogin($token);
        
        if ($checkLoginCommand instanceof ErrorCommand) {
            $checkLoginCommand->execute($conn);
            return;
        }
        
        if ($checkLoginCommand->isServer()) {
            $this->serverMessage($msg);
            return;
        }
        
        if ($checkLoginCommand->isEquipament()) {
            /** @var EquipamentCommand $command */
            $command = $this->messageService->parseEquipamentMessage($msg);
            $commandReflectionFunction = $command->execute($this->clientServer);
            /** @var ActionCommand $actionCommandReflection */
            $actionCommandReflection = $commandReflectionFunction($this->reflections);
            $sendMessageToServerFunction = $actionCommandReflection->execute($this->extruderConnection);
            if (!empty($sendMessageToServerFunction)) {
                $this->sendMessageToServerFunctions[$command->getPin()->getPin()] = $sendMessageToServerFunction;
            }
        }
    }
    
    private function serverMessage(string $msg): void
    {
        /** @var SetupCommand[] $setupCommands */
        $setupCommands = $this->messageService->parseServerSetupMessage($msg);
        foreach ($setupCommands as $setupCommand) {
            $setupCommand->execute($this->extruderConnection);
        }

        $actionCommands = $this->messageService->parseServerActionMessage($msg);
        if (!empty($actionCommands)) {
            $this->actionCommands = $actionCommands;
        }
        
        /** @var InitCommand $initCommand */
        $initCommand = $this->messageService->parseServerInitMessage($msg);
        $addReflections = $initCommand->executeActionCommands($this->extruderConnection, $this->actionCommands);
        
        if (!empty($addReflections)) {
            foreach ($addReflections as $addReflection) {
                $addReflection($this->reflections);
            }
            $initCommand->addTimePeriod($this->loop, function () {
                foreach ($this->sendMessageToServerFunctions as $sendMessageToServer) {
                    $sendMessageToServer($this->clientServer);
                }
            });
        }
    }
}

<?php declare(strict_types=1);

namespace App\Channel;

use App\Command\ActionCommand;
use App\Command\CommandConnectionInterface;
use App\Command\CommandErrorInterface;
use App\Command\CommandInterface;
use App\Command\EquipamentCommand;
use App\Command\ErrorCommand;
use App\Command\SetupCommand;
use App\Service\LoginService;
use App\Service\MessagesService;
use App\Service\ServiceInterface;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

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
     * ExtruderChannel constructor.
     * @param ServiceInterface $loginService
     * @param MessagesService $messageService
     */
    public function __construct(ServiceInterface $loginService, MessagesService $messageService)
    {
        $this->loginService = $loginService;
        $this->messageService = $messageService;
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
            /** @var CommandInterface[] $commands */
            $commands = $this->messageService->parseServerMessage($msg);
            
            /** @var SetupCommand[] $setupCommands */
            $setupCommands = $commands['setupCommands'];
            foreach ($setupCommands as $setupCommand) {
                $setupCommand->execute($this->extruderConnection);
            }
            
            /** @var ActionCommand[] $actionCommands */
            $actionCommands = $commands['actionCommands'];
            foreach ($actionCommands as $actionCommand) {
                $reflection = $actionCommand->execute($this->extruderConnection);
                $reflection($this->reflections);
            }
        }
        
        if ($checkLoginCommand->isEquipament()) {
            /** @var EquipamentCommand $command */
            $command = $this->messageService->parseEquipamentMessage($msg);
            $reflectionCommand = $command->execute($this->clientServer);
            /** @var ActionCommand $actionCommandReflection */
            $actionCommandReflection = $reflectionCommand($this->reflections);
            $actionCommandReflection->execute($this->extruderConnection);
        }
    }
}

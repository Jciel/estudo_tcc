<?php declare(strict_types=1);

namespace App\Channel;

use App\Command\ErrorCommand;
use App\Command\Factory\CommandFactory;
use App\Command\LoginCloseCommand;
use App\Command\OpenedLogin;
use App\Service\LoginService;
use App\Service\MessagesService;
use App\Service\ServiceInterface;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

/**
 * Class LoginChannel
 * @package App\Channel
 */
class LoginChannel implements MessageComponentInterface, ChannelInterface
{
    /**
     * @var LoginService $loginService
     */
    private $loginService;

    /**
     * LoginChannel constructor.
     * @param ServiceInterface $loginService
     */
    public function __construct(ServiceInterface $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn): void
    {
        $openedLoginCommand = CommandFactory::create(OpenedLogin::class);
        $openedLoginCommand->execute($conn);
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn): void
    {
        $loginCloseCommand = CommandFactory::create(LoginCloseCommand::class);
        $loginCloseCommand->execute($conn);
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        $errorCommand = CommandFactory::create(ErrorCommand::class, [$e->getMessage(), null]);
        $errorCommand->execute($conn);
        $conn->close();
    }

    /**
     * @param ConnectionInterface $conn
     * @param string $msg
     */
    public function onMessage(ConnectionInterface $conn, $msg): void
    {
        $logedInCommand = $this->loginService->login($msg, $conn);
        $logedInCommand->execute($conn);
        $conn->close();
    }
}

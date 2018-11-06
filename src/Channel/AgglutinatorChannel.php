<?php //declare(strict_types=1);
//
//namespace App\Channel;
//
//use App\ObjectValue\ConnectionLoginData;
//use App\ObjectValue\Message;
//use App\ObjectValue\TokenDataInterface;
//use App\Service\LoginService;
//use App\Service\ServiceInterface;
//use Closure;
//use Ratchet\ConnectionInterface;
//use Ratchet\MessageComponentInterface;
//use SplObjectStorage;
//
///**
// * Class AgglutinatorChannel
// * @package App\Channel
// */
//class AgglutinatorChannel implements MessageComponentInterface, ChannelInterface
//{
//    use ChannelTrait;
//
//    /**
//     * @var ConnectionInterface $agglutinatorConnection
//     */
//    protected $agglutinatorConnection;
//
//    /**
//     * @var LoginService $loginService
//     */
//    private $loginService;
//
//    /**
//     * @var SplObjectStorage $connections
//     */
//    protected $clients;
//    
//    /**
//     * AgglutinatorChannel constructor.
//     * @param ServiceInterface $loginService
//     */
//    public function __construct(ServiceInterface $loginService)
//    {
//        $this->loginService = $loginService;
//        $this->clients = new SplObjectStorage();
//    }
//
//    /**
//     * @param ConnectionInterface $conn
//     */
//    public function onOpen(ConnectionInterface $conn): void
//    {
//        $token = $this->getToken($conn->httpRequest);
//
//        /** @var ConnectionLoginData|Message $tokenData */
//        $tokenData = $this->loginService->checkLogin($token);
//
//        if ($tokenData->isError()) {
//            $conn->send(json_encode([
//                "error" => $tokenData->isError(),
//                "message" => $tokenData->getMessage(),
//                "token" => $tokenData->getToken()
//            ]));
//            $conn->close();
//            return;
//        }
//
//        if ($tokenData->isClient()) {
//            $this->clients->attach($conn);
//        }
//
//        if ($tokenData->isEquipament()) {
//            $this->extruderConnection = $conn;
//        }
//    }
//
//    /**
//     * @param ConnectionInterface $conn
//     */
//    public function onClose(ConnectionInterface $conn): void
//    {
//        $token = $this->getToken($conn->httpRequest);
//        /** @var ConnectionLoginData $tokenData */
//        $tokenData = $this->loginService->checkLogin($token);
//
//        echo "Connection closed\n";
//        
//        if ($tokenData->isError()) {
//            return;
//        }
//        
//        if ($tokenData->isClient()) {
//            $this->clients->detach($conn);
//        }
//
//        if ($tokenData->isEquipament()) {
//            $this->extruderConnection = null;
//        }
//    }
//
//    /**
//     * @param ConnectionInterface $conn
//     * @param \Exception $e
//     */
//    public function onError(ConnectionInterface $conn, \Exception $e): void
//    {
//        echo "Error {$e->getMessage()}\n";
//        $conn->close();
//    }
//
//    /**
//     * @param ConnectionInterface $conn
//     * @param string $msg
//     */
//    public function onMessage(ConnectionInterface $conn, $msg): void
//    {
//        $token = $this->getToken($conn->httpRequest);
//        /** @var ConnectionLoginData $tokenData */
//        $tokenData = $this->loginService->checkLogin($token);
//
//        $sendMessage = $this->message($tokenData, $msg);
//
//        $sendMessage($conn);
//    }
//
//    private function message(TokenDataInterface $tokenData, string $msg): Closure
//    {
//        if ($tokenData->isError()) {
//            return function (ConnectionInterface $conn) use ($tokenData): void {
//                $conn->send(json_encode([
//                    'error' => $tokenData->isError(),
//                    'message' => $tokenData->getMessage(),
//                    'token' => $tokenData->getToken()
//                ]));
//                $conn->close();
//            };
//        }
//        
//        if ($tokenData->isClient() && empty($this->extruderConnection)) {
//            return function (ConnectionInterface $conn): void {
//                $conn->send('{"error": true, "message": "Equipament disconected", "token": null}');
//            };
//        }
//
//        if ($tokenData->isEquipament() && empty($this->clients->count())) {
//            return function (ConnectionInterface $conn): void {
//                $conn->send('{"error": true, "message": "Client disconected", "token": null}');
//            };
//        }
//
//        if ($tokenData->isClient()) {
//            return function (ConnectionInterface $conn) use ($msg): void {
//                $this->agglutinatorConnection->send(json_encode([
//                    'error' => false,
//                    'message' => $msg,
//                    'token' => null
//                ]));
//            };
//        }
//
//        if ($tokenData->isEquipament()) {
//            return function (ConnectionInterface $conn) use ($msg): void {
//                foreach ($this->clients as $client) {
//                    $client->send(json_encode([
//                        'error' => false,
//                        'message' => $msg,
//                        'token' => null
//                    ]));
//                }
//            };
//        }
//    }
//}

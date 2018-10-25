<?php declare(strict_types=1);

namespace App\Channel;

use App\ObjectValue\MessageInterface;
use App\ObjectValue\TokenDataInterface;
use App\Service\MessagesService;
use Closure;
use GuzzleHttp\Psr7\Request;
use Ratchet\ConnectionInterface;

/**
 * Trait ChannelTrait
 * @package App\Channel
 */
trait ChannelTrait
{
    /**
     * @param Request $request
     * @return string
     */
    protected function getToken(Request $request): string
    {
        $queryTemp = $request->getUri()->getQuery();
        $queryTemp = explode('=', $queryTemp);
        return $queryTemp[1];
    }

    /**
     * @param MessageInterface|TokenDataInterface $tokenData
     * @param string $msg
     * @return Closure
     */
    private function message(MessageInterface $tokenData, string $msg): Closure
    {
        if ($tokenData->isError()) {
            return function (ConnectionInterface $conn) use ($tokenData) {
                $conn->send(MessagesService::errorMessage($tokenData));
                $conn->close();
            };
        }

        if ($tokenData->isServer() && empty($this->extruderConnection)) {
            return function (ConnectionInterface $conn) {
                $conn->send(MessagesService::equipamentDisconected());
            };
        }

        if ($tokenData->isEquipament() && empty($this->clientServer)) {
            return function (ConnectionInterface $conn) use ($msg) {
                array_push($this->messagesCache, $msg);
                $conn->send(MessagesService::serverDiconected());
            };
        }

        if ($tokenData->isServer()) {
            return function (ConnectionInterface $conn) use ($msg) {
                $this->extruderConnection->send(MessagesService::message($msg));
            };
        }

        if ($tokenData->isEquipament()) {
            return function (ConnectionInterface $conn) use ($msg) {
                $this->clientServer->send(MessagesService::message($msg));
            };
        }
    }
}

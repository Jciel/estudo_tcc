<?php

namespace App\Service;

use App\ObjectValue\MessageInterface;

/**
 * Class Messages
 * @package App\Service
 */
class MessagesService
{
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
        return '{"error": true, "message": "Client disconected", "token": null}';
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
            'error' => null,
            'message' => 'Login opened'
        ]);
    }
    
    public static function loginClose(): string
    {
        json_encode([
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

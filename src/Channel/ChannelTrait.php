<?php declare(strict_types=1);

namespace App\Channel;

use GuzzleHttp\Psr7\Request;

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
}

<?php declare(strict_types=1);

namespace App;


use App\Channel\Interfaces\ChannelInterface;

/**
 * Class ChannelManager
 * @package App
 */
class ChannelManager
{
    /**
     * @var array $channels
     */
    private $channels;

    /**
     * ChannelManager constructor.
     * @param array $channels
     */
    public function __construct(array $channels)
    {
        $this->channels = $channels;
    }

    /**
     * @param string $channel
     * @param ServiceManagerInterface $container
     * @return ChannelInterface
     */
    public function get(string $channel, ServiceManagerInterface $container): ChannelInterface
    {
        $channel = new $this->channels[$channel];

        return $channel($container);
    }
}

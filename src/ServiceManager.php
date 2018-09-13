<?php declare(strict_types=1);

namespace App;

use App\Service\ServiceInterface;

/**
 * Class ServiceManager
 * @package App
 */
class ServiceManager implements ServiceManagerInterface
{
    /**
     * @var ServiceInterface[] $services
     */
    private $services;
    
    /**
     * @var array $config
     */
    private $config;

    /**
     * ServiceManager constructor.
     * @param array $services
     * @param array $config
     */
    public function __construct(array $services, array $config)
    {
        $this->services = $services;
        $this->config = $config;
    }

    /**
     * @param string $service
     * @return ServiceInterface
     */
    public function get(string $service): ServiceInterface
    {
        $service = new $this->services[$service];
        
        return $service($this);
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}

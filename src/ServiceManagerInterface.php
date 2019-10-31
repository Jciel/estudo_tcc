<?php declare(strict_types=1);

namespace App;

use App\Service\Interfaces\ServiceInterface;

/**
 * Interface ServiceManagerInterface
 * @package App
 */
interface ServiceManagerInterface
{
    /**
     * @param string $service
     * @return mixed
     */
    public function get(string $service): ServiceInterface;
}

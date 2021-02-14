<?php


namespace Yao\Cache;

/**
 * Class Setter
 * @package Yao\Cache
 */
class Setter
{
    public $driver;

    public function __construct()
    {
        $driver = '\\Yao\\Cache\\Drivers\\' . ucfirst(config('cache.type'));
        $this->driver = new $driver();
    }

    public function __call($cacheCommand, $data)
    {
        return $this->driver->$cacheCommand(...$data);
    }
}

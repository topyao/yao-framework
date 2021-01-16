<?php


namespace Yao\Cache;


/**
 * 缓存操作类
 * Class Cache
 * @package Yao
 */
class Cache
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

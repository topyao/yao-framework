<?php

namespace Yao\Cache\Drivers;

use Yao\Cache\Driver;
use Yao\Facade\Config;

/**
 * Class Redis
 * @package Yao\Cache\Drivers
 */
class Redis extends Driver
{

    protected $redis;

    private function __construct()
    {
        $config = Config::get('cache.' . Config::get('cache.type'));
        $this->redis = new \Redis();
        $this->redis->connect($config['host'], $config['port']);
    }

    public function set(string $key, $value, ?int $timeout = null)
    {
        return $this->redis->set($key, $value, $timeout);
    }

    public function get(string $key)
    {
        return $this->redis->get($key);
    }
}

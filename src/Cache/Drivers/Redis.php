<?php

namespace Yao\Cache\Drivers;

use Yao\Facade\Config;

/**
 * Class Redis
 * @package Yao\Cache\Drivers
 */
class Redis
{
    protected static $instance;

    protected $redis;

    private function __construct()
    {
        Config::load('cache');
        $config = Config::get('cache.' . Config::get('cache.type'));
        $this->redis = new \Redis();
        $this->redis->connect($config['host'], $config['port']);
    }

    public static function instance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
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
<?php

namespace yao\cache;

use yao\facade\Config;

class Redis
{
    protected static $instance;

    protected $redis;

    private function __construct()
    {
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
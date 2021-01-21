<?php

namespace Yao\Db;

use Yao\Facade\Config;

/**
 * 数据库外部接口
 * Class Db
 * @package yao
 */
class Query
{
    public $driver;
    public array $config = [];

    public function __construct()
    {
        $database = Config::get('database.type');
        if (!$database) {
            throw new \Exception('数据库配置文件不存在');
        }
        $driver = '\\Yao\\Db\\Drivers\\' . ucfirst($database);
        $this->driver = new $driver($database);
        $this->collection = new \Yao\Collection();
    }

    public function __call($method, $args)
    {
        return $this->driver->$method(...$args);
    }

}

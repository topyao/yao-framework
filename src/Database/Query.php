<?php

namespace Yao\Database;

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
        $driver = '\\Yao\\Database\\Drivers\\' . ucfirst($database);
        $this->driver = new $driver($database);
    }

    public function __call($method, $args)
    {
        return $this->driver->$method(...$args);
    }

}

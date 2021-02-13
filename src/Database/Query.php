<?php

namespace Yao\Database;

use Yao\App;

/**
 * 数据库外部接口
 * Class Db
 * @package yao
 */
class Query
{

    public $driver;

    public function __construct(App $app)
    {
        $database = $app->config->get('database.type');
        if (!$database) {
            throw new \Exception('数据库配置文件不存在');
        }
        $driver = '\\Yao\\Database\\Drivers\\' . ucfirst($database);
        $this->driver = $app->make($driver, [$database],false);
    }

    public function __call($method, $args)
    {
        return $this->driver->$method(...$args);
    }

}

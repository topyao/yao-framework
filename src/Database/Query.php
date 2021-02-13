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

    /**
     * 数据库驱动
     * @var mixed|object
     */
    public $driver;

    /**
     * 初始化实例列表和配置
     * Query constructor.
     * @param App $app
     * @throws \Exception
     */
    public function __construct(App $app)
    {
        $database = $app->config->get('database.type');
        if (!$database) {
            throw new \Exception('数据库配置文件不存在');
        }
        $driver = '\\Yao\\Database\\Drivers\\' . ucfirst($database);
        $this->driver = $app->make($driver, [$database], false);
    }

    /**
     * 实际调用驱动方法的方法
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return $this->driver->$method(...$args);
    }

}

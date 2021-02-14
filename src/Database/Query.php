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
     * 容器实例
     * @var App
     */
    protected App $app;

    /**
     * 数据库类型
     * @var string
     */
    protected string $database = '';

    /**
     * 数据库驱动
     * @var string
     */
    public const DRIVER_BASE_NAMESPACE = '\\Yao\\Database\\Drivers\\';

    /**
     * 初始化实例列表和配置
     * Query constructor.
     * @param App $app
     * @throws \Exception
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->database = $app->config->get('database.type');
        if (!$this->database) {
            throw new \Exception('数据库配置文件不存在');
        }
    }

    /**
     * 实际调用驱动方法的方法
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return $this->app->invokeMethod([self::DRIVER_BASE_NAMESPACE . ucfirst($this->database), $method], $args, false, [$this->database]);
    }

}

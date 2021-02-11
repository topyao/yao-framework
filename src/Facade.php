<?php

namespace Yao;

/**
 * 门面实现基类
 * Class Facade
 * @package Yao
 */
class Facade
{
    /**
     * 是否单例，false表示非单例
     * @var bool
     */
    protected static $singleInstance = true;

    /**
     * 获取当前Facade对应类名
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
    }

    /**
     * 创建Facade实例
     * @return mixed
     */
    protected static function createFacade()
    {
        return App::instance()
            ->make(static::getFacadeClass(), [], static::$singleInstance);
    }

    /**
     * 调用实际类的方法,默认支持依赖注入
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        return App::instance()
            ->invokeMethod([static::getFacadeClass(), $method], $params, static::$singleInstance);
    }
}

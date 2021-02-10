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
    protected static $singleInstance = false;

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
        //这里需要完全用容器接管
        $class = static::getFacadeClass();
        if (!static::$singleInstance) {
            return new $class();
        }
        if (!isset(static::$instance[$class]) || !(static::$instance[$class] instanceof $class)) {
            static::$instance[$class] = new $class();
        }
        return static::$instance[$class];
    }

    /**
     * 调用实际类的方法
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $facadeClass = static::getFacadeClass();
        return App::instance()->invokeMethod([$facadeClass, $method], $params, static::$singleInstance);
    }
}

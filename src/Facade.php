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
     * 存放单例的数组
     * @var array
     */
    protected static array $instance = [];

    /**
     * 是否单例，false表示非单例
     * @var bool
     */
    protected static $singleInstance = false;

    protected static $params = [];

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
//        return Container::instance()
//            ->get(static::getFacadeClass(), static::$params, static::$singleInstance);
        $class = static::getFacadeClass();
        if (!class_exists($class)) {
            throw new \Exception('类' . $class . '不存在', 404);
        }
        if (!static::$singleInstance) {
            return new $class();
        } else {
            if (!isset(static::$instance[$class]) || !(static::$instance[$class] instanceof $class)) {
                static::$instance[$class] = new $class();
            }
            return static::$instance[$class];
        }
    }

    /**
     * 调用实际类的方法
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
//        return static::createFacade()->invoke($method, $params);
        return call_user_func_array([static::createFacade(), $method], $params);
    }
}

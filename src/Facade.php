<?php

namespace Yao;

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
        $class = static::getFacadeClass();
        if (!class_exists($class)) {
            throw new \Exception('类' . $class . '不存在', 404);
        }
        if (!static::$singleInstance) {
            return new $class();
        } else {
            if (!isset(self::$instance[$class]) || !(self::$instance[$class] instanceof $class)) {
                self::$instance[$class] = new $class();
            }
            return self::$instance[$class];
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
        return call_user_func_array([static::createFacade(), $method], $params);
    }
}

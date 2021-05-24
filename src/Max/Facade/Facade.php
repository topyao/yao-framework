<?php
declare(strict_types=1);

namespace Max\Facade;

/**
 * 门面实现基类
 * Class Facade
 * @package Max
 */
abstract class Facade
{
    /**
     * 是否单例，false表示非单例
     * @var bool
     */
    protected static $singleInstance = true;

    /**
     * 方法注入设置属性，true时所有Facade调用的方法都支持依赖注入
     * @var bool
     */
    protected static $methodInjection = false;

    /**
     * 构造函数参数列表
     * @var array
     */
    protected static $constructorArguments = [];

    /**
     * 获取当前Facade对应类名
     * @access protected
     * @return string
     */
    abstract protected static function getFacadeClass();

    /**
     * 创建Facade实例
     * @return mixed
     */
    final protected static function createFacade()
    {
        return \Max\Foundation\App::instance()->make(
            static::getFacadeClass(),
            static::$constructorArguments,
            static::$singleInstance
        );
    }

    /**
     * 调用实际类的方法,默认支持依赖注入
     * @param string $method
     * @param array $params
     * @return mixed
     */
    final public static function __callStatic(string $method, array $params)
    {
        if (static::$methodInjection) {
            return \Max\Foundation\App::instance()
                ->invokeMethod(
                    [static::getFacadeClass(), $method],
                    $params,
                    static::$singleInstance,
                    static::$constructorArguments
                );
        }
        return static::createFacade()->{$method}(...$params);
    }
}

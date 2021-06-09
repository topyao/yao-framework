<?php
declare(strict_types=1);

namespace Max\Facade;

use Max\Foundation\App;

/**
 * 门面实现基类
 * Class Facade
 * @package Max
 */
abstract class Facade
{

    /**
     * 重新实例化
     * @var bool
     */
    protected static $renew = false;

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
        return App::instance()->make(
            static::getFacadeClass(),
            static::$constructorArguments,
            static::$renew
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
            return App::instance()
                ->invokeMethod(
                    [static::getFacadeClass(), $method],
                    $params,
                    static::$renew,
                    static::$constructorArguments
                );
        }
        return static::createFacade()->{$method}(...$params);
    }
}

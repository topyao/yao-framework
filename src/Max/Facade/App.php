<?php
declare(strict_types=1);

namespace Max\Facade;

/**
 * @method static get(string $class_name) 获取实例化后的对象
 * @method static has(string $class_name) 判断实例是否存在
 * @method static bind(string $id, string $className) 绑定类到标识ID
 * @method static make(string $abstract, array $arguments = [], bool $renew = false) 使用容器实例化类
 * @method static remove(string $abstract) 注销实例
 * @method static invokeMethod(array $callable, array $arguments = [], bool $renew = false, array $constructorParameters = []) 对调用的方法实现依赖注入
 * Class App
 * @package Max\Facade
 */
class App extends Facade
{

    protected static function getFacadeClass()
    {
        return 'app';
    }

}
<?php

namespace Yao;

class Container implements \ArrayAccess, \Countable
{
    /**
     * 依赖注入的类实例
     * @var array
     */
    protected static array $instances = [];

    /**
     * 反射类实例
     * @var
     */
    protected $reflectionClass;

    /**
     * 绑定的类名
     * @var array|string[]
     */
    protected static array $bind = [
        'request' => \Yao\Http\Request::class,
        'validate' => \App\Http\Validate::class,
        'file' => File::class,
        'env' => Env::class,
        'config' => Config::class,
        'app' => App::class,
        'view' => View::class,
        'route' => \Yao\Route::class
    ];

    /**
     * 获取绑定类名
     * @param $name
     * @return mixed|string
     */
    protected static function _getBindClass(string $name)
    {
        return static::$bind[strtolower($name)] ?? $name;
    }

    /**
     * 获取类对象，支持依赖注入
     * @param $abstract
     * @param array $arguments
     * @param false $singleInstance
     * @return mixed
     * @throws \ReflectionException
     */
    public static function make($abstract, $arguments = [], $singleInstance = false)
    {
        $abstract = static::_getBindClass($abstract);
        if (!isset(static::$instances[$abstract]) || !$singleInstance) {
            $reflectionClass = new \ReflectionClass($abstract);
            if (null === ($constructor = $reflectionClass->getConstructor())) {
                static::$instances[$abstract] = new $abstract(...$arguments);
            } else if ($constructor->isPublic()) {
                $parameters = $constructor->getParameters();
                $injectClass = static::_getInjectObject($parameters);
                static::$instances[$abstract] = new $abstract(...[...$arguments, ...$injectClass]);
            }
        }
        return static::$instances[$abstract];
    }


    /**
     * 通过参数列表获取注入对象数组
     * @param $parameters
     * @return array
     */
    protected static function _getInjectObject($parameters)
    {
        $injectClass = [];
        foreach ($parameters as $parameter) {
            if (!is_null($class = $parameter->getClass())) {
                $className = $class->getName();
                $injectClass[] = new $className();
            }
        }
        return $injectClass;
    }

    /**
     * 调用类的方法
     * @param array $callable
     * @param array $arguments
     * @param false $singleInstance
     * @param array $constructorParameters
     * @return mixed
     */
    public static function invokeMethod(array $callable, array $arguments = [], bool $singleInstance = false, array $constructorParameters = [])
    {
        [$class, $method] = [static::_getBindClass($callable[0]), $callable[1]];
        static::make($class, $constructorParameters, $singleInstance);

        $parameters = (new \ReflectionClass($class))->getMethod($method)->getParameters();

//        $parameters = $this->reflectionClass->getMethod($method)->getParameters();
        $injectClass = static::_getInjectObject($parameters);
        return call_user_func_array([static::$instances[$class], $method], [...$arguments, ...$injectClass]);
    }

    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
    }

    public function offsetGet($offset)
    {
        // TODO: Implement offsetGet() method.
    }

    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }

    public function count()
    {
        return count(static::$instances);
    }

}

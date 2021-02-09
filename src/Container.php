<?php

namespace Yao;

use Psr\Container\ContainerInterface;
use Yao\Exception\ContainerException;

class Container implements ContainerInterface, \ArrayAccess
{

    /**
     * 依赖注入的类实例
     * @var array
     */
    protected static array $instances = [];

    /**
     * 绑定的类名
     * @var array|string[]
     */
    protected array $bind = [];


    public static function instance()
    {
        if (!isset(static::$instances[static::class]) || !static::$instances[static::class] instanceof static) {
            static::$instances[static::class] = new static();
        }
        return static::$instances[static::class];
    }


    public function set($abstract, $instance)
    {
        static::$instances[$abstract] = $instance;
    }

    public function get($abstract)
    {
        $abstract = $this->_getBindClass($abstract);
        if ($this->has($abstract)) {
            return static::$instances[$abstract];
        }
        throw new ContainerException("实例'{$abstract}'没有找到");
    }

    public function has($abstract)
    {
        $abstract = $this->_getBindClass($abstract);
        return isset(static::$instances[$abstract]);
    }


    public function bind($id, $className)
    {
        $this->bind[$id] = $className;
    }


    /**
     * 获取绑定类名
     * @param $name
     * @return mixed|string
     */
    protected function _getBindClass(string $name): string
    {
        return $this->bind[strtolower($name)] ?? $name;
    }

    /**
     * 注入的外部接口方法
     * @param string $abstract
     * @param array $arguments
     * @param bool $singleInstance
     * @return mixed
     */
    public function make(string $abstract, array $arguments = [], bool $singleInstance = true)
    {
        $abstract = $this->_getBindClass($abstract);

        //非单例会强制刷新当前存在的单例实例
        if (!$singleInstance) {
            $this->remove($abstract);
            return $this->_inject($abstract, $arguments);
        }

        if (!$this->has($abstract)) {
            $this->set($abstract, $this->_inject($abstract, $arguments));
        }

        return $this->get($abstract);
    }

    /**
     * 注销实例
     * @param $abstract
     */
    public function remove($abstract)
    {
        $abstract = $this->_getBindClass($abstract);
        if ($this->has($abstract)) {
            unset(self::$instances[$abstract]);
            return true;
        }
        return false;
    }

    private function _inject($abstract, $arguments)
    {
        $reflectionClass = new \ReflectionClass($abstract);
        if (null === ($constructor = $reflectionClass->getConstructor())) {
            return new $abstract(...$arguments);
        } else if ($constructor->isPublic()) {
            $parameters = $constructor->getParameters();
            $injectClass = $this->_getInjectObject($parameters);
            return new $abstract(...[...$arguments, ...$injectClass]);
        } else {
            throw new ContainerException("类{$abstract}不能实例化！");
        }
    }


    /**
     * 调用类的方法
     * @param array $callable
     * 可调用的类和方法数组['className','methodName']
     * @param array $arguments
     * 给方法传递的参数
     * @param false $singleInstance
     * true表示单例
     * @param array $constructorParameters
     * 给构造方法传递的参数
     * @return mixed
     */
    public function invokeMethod(array $callable, array $arguments = [], bool $singleInstance = true, array $constructorParameters = [])
    {
        [$abstract, $method] = [$this->_getBindClass($callable[0]), $callable[1]];
        $instance = $this->make($abstract, $constructorParameters, $singleInstance);
        $parameters = (new \ReflectionClass($abstract))->getMethod($method)->getParameters();
        $injectClass = $this->_getInjectObject($parameters);
        return call_user_func_array([$instance, $method], [...$arguments, ...$injectClass]);
    }


    /**
     * 通过参数列表获取注入对象数组
     * @param $parameters
     * @return array
     */
    protected function _getInjectObject(array $parameters)
    {
        //此处有bug，所有注入的类都成了单例的了
        $injectClass = [];
        foreach ($parameters as $parameter) {
            if (!is_null($class = $parameter->getClass())) {
                $injectClass[] = $this->make($class->getName(), [], true);
            }
        }
        return $injectClass;
    }

    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
    }

    public function offsetGet($abstract)
    {
        return $this->make($abstract);
    }

    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }


    public function __get($abstract)
    {
        return $this->get($abstract);
    }

}

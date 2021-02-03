<?php

namespace Yao;

use Psr\Container\ContainerInterface;
use Yao\Exception\ContainerException;

class Container implements ContainerInterface, \ArrayAccess
{
    private static $instance;


    /**
     * 依赖注入的类实例
     * @var array
     */
    protected array $instances = [];
    /**
     * 当前实例化并调用方法的类名
     * @var $instance
     */
//    protected static $abstract;
    /**
     * 绑定的类名
     * @var array|string[]
     */
    protected array $bind = [
        'request' => Http\Request::class,
        'validate' => \App\Http\Validate::class,
        'file' => File::class,
        'env' => Env::class,
        'config' => Config::class,
        'app' => App::class,
        'view' => View\Render::class,
        'route' => Route\Route::class
    ];


    public static function instance()
    {
        if (!static::$instance instanceof static) {
            static::$instance = new static();
        }
        return static::$instance;
    }


    public function set($abstract, $instance)
    {
        $this->instances[$abstract] = $instance;
    }

    public function get($abstract)
    {
        $abstract = $this->_getBindClass($abstract);
        if ($this->has($abstract)) {
            return $this->instances[$abstract];
        }
        throw new ContainerException("实例'{$abstract}'没有找到");
    }

    public function has($abstract)
    {
        $abstract = $this->_getBindClass($abstract);
        return isset($this->instances[$abstract]);
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
     * 获取类对象，支持依赖注入
     * @param $abstract
     * 需要实例化的类
     * @param array $arguments
     * 给构造方法传递的参数
     * @param bool $singleInstance
     * 为true表示单例
     * @return mixed
     * @throws \ReflectionException
     */
    public function make(string $abstract, array $arguments = [], bool $singleInstance = true)
    {
        $abstract = $this->_getBindClass($abstract);
        if (!$this->has($abstract) || !$singleInstance) {
            $reflectionClass = new \ReflectionClass($abstract);
            if (null === ($constructor = $reflectionClass->getConstructor())) {
                $this->set($abstract, new $abstract(...$arguments));
            } else if ($constructor->isPublic()) {
                $parameters = $constructor->getParameters();
                $injectClass = $this->_getInjectObject($parameters);
                $this->set($abstract, new $abstract(...[...$arguments, ...$injectClass]));
            } else {
                throw new ContainerException("类{$abstract}不能实例化！");
            }
        }
        return $this->get($abstract);
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


}

<?php
declare(strict_types=1);

namespace Max\Foundation;

use Psr\Container\ContainerInterface;
use Max\Exception\ContainerException;

/**
 * 一个简单的容器类
 * Class Container
 * @package Max
 */
class Container implements ContainerInterface, \ArrayAccess
{

    /**
     * 依赖注入的类实例
     * @var array
     */
    protected static $instances = [];

    /**
     * 绑定的类名
     * @var array|string[]
     */
    protected $bind = [];

    /**
     * 实例标识
     * @var bool
     */
    protected $refreshable = false;

    /**
     * 单例模式获取类实例
     * 从static::$instances中实例，和依赖注入获取相同实例
     * @return static
     */
    public static function instance()
    {
        if (!isset(static::$instances[static::class])) {
            static::$instances[static::class] = new static();
        }
        return static::$instances[static::class];
    }

    /**
     * 将实例化的类存放到数组中
     * @param string $abstract
     * 类名
     * @param object $instance
     * 实例话后的对象
     */
    public function set(string $abstract, $instance)
    {
        static::$instances[$this->bound($abstract)] = $instance;
    }

    /**
     * 获取存在的实例
     * @param string $id
     * 类的标识[完整类名]
     * @return mixed
     */
    public function get(string $id)
    {
        $abstract = $this->bound($id);
        if ($this->has($abstract)) {
            return static::$instances[$abstract];
        }
        throw new ContainerException('No instance found: ' . $abstract);
    }

    /**
     * 判断类的实例是否存在
     * @param string $id
     * 类的标识[完整类名]
     * @return bool
     */
    public function has(string $id)
    {
        return isset(static::$instances[$this->bound($id)]);
    }

    /**
     * 添加绑定类的标识
     * @param string $id
     * 绑定的类标识
     * @param string $className
     * 绑定的类名
     */
    public function bind(string $id, string $className)
    {
        $this->bind[$id] = $className;
    }


    /**
     * 获取绑定类名
     * @param $name
     * @return string
     */
    public function bound(string $name): string
    {
        return $this->bind[strtolower($name)] ?? $name;
    }

    /**
     * 注入的外部接口方法
     * @param string $abstract
     * 需要实例化的类名
     * @param array $arguments
     * 索引数组的参数列表
     * @param bool $singleInstance
     * 是否单例，true为单例，false为非单例
     * @return mixed
     */
    public function make(string $abstract, array $arguments = [], bool $singleInstance = true)
    {
        $abstract = $this->bound($abstract);
        if (!$singleInstance) {
            //非单例会强制刷新当前存在的单例实例
            $this->remove($abstract);
            //返回依赖注入后的实例
            return $this->resolve($abstract, $arguments);
        }
        if (!$this->has($abstract)) {
            $concrete = $this->resolve($abstract, $arguments);
            if ($this->refreshable) {
                $this->refreshable = false;
                return $concrete;
            }
            $this->set($abstract, $concrete);
        }
        return $this->get($abstract);
    }


    /**
     * 解除类的绑定
     * @param string $id
     * @return bool
     */
    public function unbind(string $id): bool
    {
        if (isset($this->bind[$id])) {
            unset($this->bind[$id]);
            return true;
        }
        return false;
    }

    /**
     * 注销实例
     * @param string $abstract
     * @return bool
     */
    public function remove(string $abstract): bool
    {
        $abstract = $this->bound($abstract);
        if ($this->has($abstract)) {
            unset(self::$instances[$abstract]);
            return true;
        }
        return false;
    }

    /**
     * @param string $abstract
     * @param array $arguments
     * @return object
     * @throws \ReflectionException
     */
    protected function resolve(string $abstract, array $arguments)
    {
        $arguments       = array_values($arguments);
        $reflectionClass = new \ReflectionClass($abstract);
        if ($reflectionClass->hasProperty('__refreshable')) {
            $refreshable = $reflectionClass->getProperty('__refreshable');
            if ($refreshable->isStatic() && $refreshable) {
                $this->refreshable = true;
            }
        }
        //构造方法不存在直接实例化
        if ($reflectionClass->hasMethod('__setter')) {
            $setter = $reflectionClass->getMethod('__setter');
            if ($setter->isPublic() && $setter->isStatic()) {
                return $setter->invokeArgs(null, $this->bindParams($setter, $arguments));
            }
        }
        if (null === ($constructor = $reflectionClass->getConstructor())) {
            return new $abstract(...$arguments);
        }
//构造方法为public
        if ($constructor->isPublic()) {
            //通过构造方法的参数列表注入实例
            return new $abstract(...$this->bindParams($constructor, $arguments));
        }
        throw new ContainerException('Cannot initialize class: ' . $abstract);
    }


    /**
     * 调用类的方法
     * @param array $callable
     * 可调用的类或者实例和方法数组[$class|$concrete, $method]
     * @param array $arguments
     * 给方法传递的参数
     * @param false $singleInstance
     * true表示单例
     * @param array $constructorParameters
     * 给构造方法传递的参数
     * @return mixed
     */
    public function invokeMethod(array $callable, $arguments = [], bool $singleInstance = true, array $constructorParameters = [])
    {
        [$abstract, $method] = [$callable[0], $callable[1]];
        if (is_string($abstract)) {
            $abstract = $this->bound($abstract);
        }
        $reflectionMethod = (new \ReflectionClass($abstract))->getMethod($method);
        if ($reflectionMethod->isPublic()) {
            $injectArguments = $this->bindParams($reflectionMethod, (array)$arguments);
            if ($reflectionMethod->isStatic()) {
                return $reflectionMethod->invokeArgs(null, $injectArguments);
            }
            if (!is_object($abstract)) {
                $abstract = $this->make($abstract, $constructorParameters, $singleInstance);
            }
            return $abstract->{$method}(...$injectArguments);
        }
        throw new ContainerException('Unable to call method: ' . $method);
    }

    /**
     * 直接向容器推送实例
     * @param string $id
     * @param $concrete
     * @return bool
     */
    public function push(string $id, $concrete): bool
    {
        if (isset(static::$instances[$this->bound($id)])) {
            return false;
        }
        array_push(static::$instances, $concrete);
        return true;
    }

    /**
     * 依赖注入调用闭包
     * @param \Closure $function
     * @param array $arguments
     * @return mixed
     * @throws \ReflectionException
     */
    public function invokeFunc(\Closure $function, array $arguments = [])
    {
        return $function(...$this->bindParams(
            (new \ReflectionFunction($function)),
            $arguments
        ));
    }


    /**
     * 反射获取方法的参数列表
     * @param \ReflectionFunctionAbstract $reflectionMethod
     * 反射方法
     * @param array $arguments
     * 用户传入的参数
     * @return array
     */
    protected function bindParams(\ReflectionFunctionAbstract $reflectionMethod, array $arguments): array
    {
        $dependences = $reflectionMethod->getParameters();
        $injection   = [];
        foreach ($dependences as $dependence) {
            //TODO $dependence->getType()->isBuiltIn()
            //如果是一个类,这里可能需要对闭包进行注入
            if (!is_null($class = $dependence->getClass()) && 'Closure' !== $class->getName()) {
                //使用容器实例化该类并存放到reject中
                $injection[] = $this->make($class->getName());
            } else if (!empty($arguments)) {
                //按顺序将非实例的参数存放到参数列表
                $injection[] = array_shift($arguments);
            }
        }
        return $injection;
    }


    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($abstract)
    {
        return $this->make($abstract);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->get($offset);
    }

    public function __get($abstract)
    {
        return $this->get($abstract);
    }

    private function __clone()
    {
    }

    private function __construct()
    {
    }

    public function __destruct()
    {
        static::$instances = null;
    }

}

<?php
declare(strict_types=1);

namespace Yao;

use Psr\Container\ContainerInterface;
use Yao\Exception\ContainerException;
use Yao\Traits\SingleInstance;

/**
 * 一个简单的容器类
 * Class Container
 * @package Yao
 */
class Container implements ContainerInterface, \ArrayAccess
{

    use SingleInstance;

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

    /**
     * 单例模式获取类实例
     * @return static
     */
    public static function instance()
    {
        if (!isset(static::$instances[static::class]) || !static::$instances[static::class] instanceof static) {
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
    public function set(string $abstract, object $instance): void
    {
//        $abstract = $this->_getBindClass($abstract);
        static::$instances[$abstract] = $instance;
    }

    /**
     * @param string $id
     * 类的标识[完整类名]
     * @return mixed
     */
    public function get($id)
    {
        $abstract = $this->_getBindClass($id);
        if ($this->has($abstract)) {
            return static::$instances[$abstract];
        }
        throw new ContainerException("'{$abstract}'还没有实例化！");
    }

    /**
     * 判断类的实例是否存在
     * @param string $id
     * 类的标识[完整类名]
     * @return bool
     */
    public function has($id)
    {
        $abstract = $this->_getBindClass($id);
        return isset(static::$instances[$abstract]);
    }

    /**
     * 添加绑定类的标识
     * @param string $id
     * 绑定的类标识
     * @param string $className
     * 绑定的类名
     */
    public function bind(string $id, string $className): void
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
     * 需要实例化的类名
     * @param array $arguments
     * 索引数组的参数列表
     * @param bool $singleInstance
     * 是否单例，true为单例，false为非单例
     * @return mixed
     */
    public function make(string $abstract, array $arguments = [], bool $singleInstance = true): object
    {
        $abstract = $this->_getBindClass($abstract);
        $arguments = array_values($arguments);
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
    public function remove(string $abstract): bool
    {
        $abstract = $this->_getBindClass($abstract);
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
    private function _inject(string $abstract, array $arguments): object
    {
        $reflectionClass = new \ReflectionClass($abstract);
        if (null === ($constructor = $reflectionClass->getConstructor())) {
            return new $abstract(...$arguments);
        }
        if ($constructor->isPublic()) {
            $parameters = $constructor->getParameters();
            $injectClass = $this->_getInjectObject($parameters);
            return new $abstract(...[...$arguments, ...$injectClass]);
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
    protected function _getInjectObject(array $parameters): array
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

}

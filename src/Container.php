<?php

namespace Yao;

class Container
{
    use \Yao\Traits\SingleInstance;

    /**
     * 依赖注入的类实例
     * @var array
     */
    private array $instances = [];

    /**
     *
     * @var
     */
    private $reflectionClass;

    /**
     * 绑定的类名
     * @var array|string[]
     */
    private array $bind = [
        'request' => \Yao\Http\Request::class,
        'validate' => \App\Http\Validate::class,
        'file' => File::class,
        'env' => Env::class,
        'config' => Config::class,
        'app' => App::class,
        'view' => View::class,
        'route' => \Yao\Route::class,
        'db' => \Yao\Db::class,
    ];

    /**
     * 获取绑定类名
     * @param $name
     * @return mixed|string
     */
    private function _getBindClass(string $name)
    {
        return $this->bind[strtolower($name)] ?? $name;
    }

    /**
     * 获取类对象，支持依赖注入
     * @param $abstract
     * @param array $arguments
     * @param false $singleInstance
     * @return mixed
     * @throws \ReflectionException
     */
    public function make($abstract, $arguments = [], $singleInstance = false)
    {
        $abstract = $this->_getBindClass($abstract);
        $this->reflectionClass = new \ReflectionClass($abstract);
        if (!isset($this->instances[$abstract]) || !$singleInstance) {
            if (null === ($constructor = $this->reflectionClass->getConstructor())) {
                $this->instances[$abstract] = new $abstract(...$arguments);
            } else if ($constructor->isPublic()) {
                $parameters = $constructor->getParameters();
                $injectClass = $this->_getInjectObject($parameters);
                $this->instances[$abstract] = new $abstract(...[...$arguments, ...$injectClass]);
            }
        }
        return $this->instances[$abstract];
    }


    /**
     * 通过参数列表获取注入对象数组
     * @param $parameters
     * @return array
     */
    private function _getInjectObject($parameters)
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
    public function invokeMethod(array $callable, array $arguments = [], bool $singleInstance = false, array $constructorParameters = [])
    {
        [$class, $method] = [$this->_getBindClass($callable[0]), $callable[1]];
        $this->make($class, $constructorParameters, $singleInstance);

        $parameters = (new \ReflectionClass($class))->getMethod($method)->getParameters();

//        $parameters = $this->reflectionClass->getMethod($method)->getParameters();
        $injectClass = $this->_getInjectObject($parameters);
        return call_user_func_array([$this->instances[$class], $method], [...$arguments, ...$injectClass]);
    }

}

<?php

namespace Yao;

class Container
{
    use \Yao\Traits\SingleInstance;

    public array $app = [];

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

    private string $class;

    /**
     * 获取完整类名
     * @param $class
     */
    private function _getClassName($class): void
    {
        if (!class_exists($class)) {
            $class = strtolower($class);
            if (!array_key_exists($class, $this->bind)) {
                throw new \Exception('类不存在');
            }
            $class = $this->bind[$class];
        }
        $this->class = $class;
    }

    /**
     * @param string $class
     * @param array $arguments
     * @param bool $singleInstance
     * @return mixed
     * @throws \Exception
     */
    public function get(string $class, array $arguments = [], bool $singleInstance = false)
    {
        $this->getInject($class, $arguments, $singleInstance);
        return self::$instance;
    }

    public function getInject(string $class, array $arguments = [], bool $singleInstance = false)
    {
        $this->_getClassName($class);
        if (!$singleInstance || !array_key_exists($this->class, $this->app)) {
            $this->app[$this->class] =
                (false != ($method = $this->_getMethod('__construct')))
                    ? new $this->class(...$arguments, ...$this->_inject($method))
                    : new $this->class();
        }
        return $this->app[$this->class];
    }


    private function _inject($method): array
    {
        $params = [];
        foreach ($method->getParameters() as $p) {
            if (null != ($injectClass = $p->getType())) {
                if (!class_exists($injectClass = ($injectClass->getName()))) {
                    if (array_key_exists(strtolower($injectClass), $this->bind)) {
                        $injectClass = $this->bind[$injectClass];
                    }
                }
                if (class_exists($injectClass)) {
                    if (!array_key_exists($injectClass, $this->app)) {
//                        $this->app[$injectClass] = new $injectClass();
                        $this->app[$injectClass] = $this->getInject($injectClass);
                    }
                    $params[] = $this->app[$injectClass];
                }
            }
        }
        return $params;
    }

    public function invoke($method, $arguments = [])
    {
        if (false != ($RefMethod = $this->_getMethod($method))) {
            $arguments = is_array($arguments) ? $arguments : [$arguments];
            return call_user_func_array([$this->app[$this->class], $method], [...$arguments, ...$this->_inject($RefMethod)]);
        }
    }

    private function _getMethod($method)
    {
        if (!$this->_getReflectionClass($this->class)->hasMethod($method)) {
            return false;
        }
        return $this->_getReflectionClass($this->class)->getMethod($method);
    }

    private function _getReflectionClass($class)
    {
        if (!class_exists($class)) {
            if (!array_key_exists($class, $this->bind)) {
                throw new \Exception("类{$class}不存在！");
            }
            $class = $this->bind[$class];
        }
        return new \ReflectionClass($class);
    }
}
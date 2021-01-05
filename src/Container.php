<?php

namespace Yao;


class Container
{
    const BINDCLASS = [
        'Request' => \Yao\Http\Request::class,
        'Validate' => \App\Http\Validate::class,
        'File' => File::class,
        'Env' => Env::class,
        'Config' => Config::class,
        'App' => App::class,
        'View' => View::class
    ];

    private $instance = [];

    private static $container;

    private static function _getInstance()
    {
        if (!static::$container instanceof static) {
            static::$container = new static;
        }
        return static::$container;
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([static::_getInstance(), '_' . $name], $arguments);
    }

    private function __clone()
    {
    }

    private function __construct()
    {
    }

    private function _getClass($class)
    {
        if (!is_string($class)) {
            if (is_null($class = $class->getType())) {
                throw new \Exception('传递的参数有问题');
            }
            $class = $class->getName();
        }
        if (!class_exists($class)) {
            $class = ltrim(strrchr($class, '\\'), '\\');
            if (!isset(self::BINDCLASS[$class])) {
                throw new \Exception('类' . $class . '不存在');
            } else {
                $class = self::BINDCLASS[$class];
            }
        }
        return $class;
    }

    public function get($class)
    {
        $reflectionClass = new \ReflectionClass($this->_getClass($class));
        return $reflectionClass;
    }

    public function getParams($class, $method)
    {
        $params = [];
        foreach ($this->get($class)->getMethod($method)->getParameters() as $param) {
            $params[] = $param->getType();
        }
        return $params;
    }


    private function _inject($class, $inject, $params, $method)
    {
        foreach ($inject as $j) {
            $injectClass = $this->_getClass($j);
            $params[] = new $injectClass;
        }
        return call_user_func_array([new $class(), $method], $params);
    }

    private function _create($class, $method, $params)
    {
        $methodParams = $this->get($class)->getMethod($method)->getParameters();
        $inject = array_diff_key($methodParams, $params);
        return $this->_inject($class, $inject, $params, $method);
    }
}

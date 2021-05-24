<?php

namespace Max;

use Max\Foundation\App;

abstract class Manager
{

    /**
     * App实例
     * @var App
     */
    protected $app;

    /**
     * 基础命名空间
     * @var string
     */
    protected $namespace = '';

    /**
     * 保存在容器中
     * @var bool
     */
    protected $singleInstance = true;

    /**
     * 构造函数的参数
     * @var array
     */
    protected $constructParameters = [];

    /**
     * 最终扩展的类名
     * @var string
     */
    protected $extension = '';

    final public function __construct(App $app)
    {
        $this->app       = $app;
        $this->extension = $this->namespace . ucfirst($this->getClass());
    }

    /**
     * 扩展的类名
     * @return string
     */
    abstract protected function getClass(): string;

    /**
     * 实际调用类的方法
     * @param $method
     * @param $vars
     * @return mixed
     */
    final public function __call($method, $vars)
    {
        return $this->app
            ->make($this->extension, $this->constructParameters, $this->singleInstance)
            ->{$method}(...$vars);
    }

}
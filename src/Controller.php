<?php

namespace Yao;

use Yao\Http\Middleware;

/**
 * Class Controller
 * @package Yao
 */
abstract class Controller
{

    public $middleware = [];

    public function __construct()
    {
        $this->init();
        $this->_registerMiddleware();
    }

    protected function init()
    {
    }

    final private function _registerMiddleware()
    {
        $middleware = new Middleware();
        $middleware->set($this->middleware);
    }

    /**
     * @param string $class 验证器完整类名
     * @param array $data 验证数据
     * @param array $notice 验证失败提示消息
     */
    protected function validate(string $class = \App\Http\Validate::class, array $data = [], array $notice = [])
    {
        return (new $class($data))->notice($notice);
    }


    /**
     * 静态方式调用类中的方法
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        if (method_exists($controller = new static, $method)) {
            return call_user_func_array([$controller, $method], $args);
        }
        throw new \Exception('类"' . static::class . '"中的方法"' . $method . '"不存在！');
    }
}

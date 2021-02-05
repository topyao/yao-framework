<?php

namespace Yao;

use Yao\Facade\Request;
use Yao\Http\Middleware;

/**
 * Class Controller
 * @package Yao
 */
abstract class Controller
{

    public $middleware = [];

    protected $app;
    protected $request;

    public function __construct(App $app, \Yao\Http\Request $request)
    {
        $this->app = $app;
        $this->request = $request;
        $this->init();
        $this->_registerMiddleware();
    }

    protected function init()
    {
    }

    final private function _registerMiddleware()
    {
        $this->app[Middleware::class]->set($this->middleware, Request::method(), Request::path());
    }

    /**
     * @param string $class 验证器完整类名
     * @param array $data 验证数据
     * @param array $notice 验证失败提示消息
     */
    protected function validate(string $class = \App\Http\Validate::class, array $data = [], array $notice = [])
    {
        return $this->app
            ->make($class, $data, false)
            ->notice($notice)
            ->check();
//        return (new $class($data))->notice($notice)->check();
    }


}

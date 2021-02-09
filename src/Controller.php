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

    protected $app;
    protected $request;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $app->request;
        $this->init();
//        $this->_registerMiddleware();
    }

    protected function init()
    {
    }

    final protected function _registerMiddleware()
    {
        $this->app[Middleware::class]->set($this->middleware, $this->request->method(), $this->request->path());
    }

    /**
     * @param string $class 验证器完整类名
     * @param array $data 验证数据
     * @param array $notice 验证失败提示消息
     */
    final protected function validate(string $class = \App\Http\Validate::class, array $data = [], array $notice = [])
    {
        return (new $class($data))->notice($notice)->check();
    }


}

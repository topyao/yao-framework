<?php

namespace Yao\Http;

use Yao\App;

/**
 * 基础控制器
 * Class Controller
 * @package Yao\Http
 */
abstract class Controller
{

    /**
     * 控制器中间件列表
     * @var array
     */
    public $middleware = [];

    /**
     * 容器实例
     * @var App
     */
    protected App $app;

    /**
     * 请求实例
     * @var Request
     */
    protected Request $request;

    /**
     * 初始化实例列表和配置
     * Controller constructor.
     * @param App $app
     */
    final public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $app->request;
        $this->init();
//        $this->_registerMiddleware();
    }

    /**
     * 用户可自定义的初始化方法
     */
    protected function init()
    {
    }

    /**
     * 控制器中间件注册方法
     */
    final protected function _registerMiddleware()
    {
        $this->app->middleware
            ->set($this->middleware, $this->request->method(), $this->request->path());
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

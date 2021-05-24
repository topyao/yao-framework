<?php
declare(strict_types=1);

namespace Max\Http;

use Max\Foundation\App;

/**
 * 基础控制器
 * Class Controller
 * @package Max\Http
 */
abstract class Controller
{

    /**
     * 控制器中间件列表
     * @var array
     */
    protected $middleware = [];

    /**
     * 容器实例
     * @var App
     */
    protected $app;

    /**
     * 请求实例
     * @var Request
     */
    protected $request;

    /**
     * 初始化实例列表和配置
     * Controller constructor.
     * @param App $app
     */
    final public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $app->request;
        $this->app['middleware']->through((function () {
            $middlewares = [];
            foreach ($this->middleware as $middleware => $actions) {
                if (is_string($actions)) {
                    if ('*' == $actions) {
                        $middlewares[] = $middleware;
                    }
                } else if (in_array($this->request->action(), $actions)) {
                    $middlewares[] = $middleware;
                }
            }
            return $middlewares;
        })());
        if (method_exists($this, 'init')) {
            $this->app->invokeMethod([$this, 'init']);
        }
    }

}

<?php
declare(strict_types=1);

namespace Max\Http;

use Max\App;

class Middleware
{

    /**
     * 容器实例
     * @var App
     */
    protected $app;

    /**
     * @var Request
     */
    protected $request;

    /**
     * 中间件队列
     * @var array
     */
    protected $middlewares = [];

    /**
     * Middleware constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 传递中间件
     * @param $middleware
     * @return $this
     */
    public function through($middleware)
    {
        $this->middlewares = (array)$middleware;
        return $this;
    }

    /**
     * 寻求有缘人帮忙重写中间件
     * @param $request
     * @return $this
     */
    public function then($request)
    {
        while ($middleware = array_pop($this->middlewares)) {
            if (!class_exists($middleware)) {
                throw new \Exception("中间件不存在：{$middleware}");
            }
            $request = function () use ($middleware, $request) {
                $response = (new $middleware)->handle($this->app->request, function (Request $next) use ($request) {
                    return $this->app->response->body($request);
                });
                if (!$response instanceof Response) {
                    throw new \Exception('中间件操作方法必须返回Response实例!');
                }
                return $response;
            };
        }
        $this->request = $request;
        return $this;
    }

    /**
     * 添加中间件
     * @param string $middleware
     */
    public function add(string $middleware)
    {
        array_push($this->middlewares, $middleware);
    }

    /**
     * 返回执行结果
     * @return Request
     */
    public function end()
    {
        return $this->request;
    }

}

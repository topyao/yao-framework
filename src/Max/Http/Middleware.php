<?php
declare(strict_types=1);

namespace Max\Http;

use Max\Foundation\App;

class Middleware
{

    /**
     * 容器实例
     * @var App
     */
    protected $app;


    protected $middlewares = [];


    /**
     * Middleware constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function through($middleware)
    {
        $this->middlewares = (array)$middleware;
        return $this;
    }

    /**
     * 不完整的中间件
     * @param $request
     * @return \Closure
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

    public function end()
    {
        return $this->request;
    }

}

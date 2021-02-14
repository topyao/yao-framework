<?php


namespace Yao\Http;


class Middleware
{

    /**
     * 中间件注册方法
     * @var array
     */
    public $middleware = [];

    public function handle($request, \Closure $next)
    {
    }


    public function make()
    {
    }


    public function before()
    {
    }


    public function after()
    {
    }

    public function middleware($request, \Closure $closure)
    {
    }

    public function set($middleware, $method, $path)
    {
        $this->middleware[$method][$path] = [...($this->middleware[$method][$path] ?? []), ...(array)$middleware];
    }

    public function get()
    {
        return $this->middleware[\Yao\Facade\Request::method()][\Yao\Facade\Request::path()];
    }
}

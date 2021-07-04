<?php

namespace Max\Http\Route;

use Max\App;
use Max\Exception\RouteNotFoundException;

class Dispatcher
{

    /**
     * App
     * @var App
     */
    protected $app;

    /**
     * @var
     */
    protected $route;

    /**
     * 路由中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 路由后缀
     * @var string
     */
    protected $ext = '';

    /**
     * 缓存
     * @var bool
     */
    protected $cache = false;

    /**
     * 允许跨域的域名
     * @var array
     */
    protected $allowOrigin = [];

    /**
     * 初始化参数列表
     * Dispatcher constructor.
     * @param array $router
     * @param App $app
     */
    public function __construct(array $router, App $app)
    {
        foreach ($router as $key => $value) {
            $this->$key = $value;
        }
        $this->app = $app;
    }

    /**
     * 调度
     * @return mixed
     * @throws RouteNotFoundException
     * @throws \ReflectionException
     */
    public function dispatch()
    {
        //TODO miss路由bug
        if (is_string($this->route)) {
            if ('C:' === substr($this->route, 0, 2)) {
                $this->route = \Opis\Closure\unserialize($this->route);
            } else {
                $callable = explode('@', $this->route, 2);
                if (!isset($callable[1])) {
                    throw new RouteNotFoundException();
                }
                $this->route = ['App\\Http\\Controllers\\' . implode('\\', array_map(function ($value) {
                        return ucfirst($value);
                    }, explode('/', $callable[0]))), $callable[1]];
            }
        }
        if (!empty($cache)) {
            $this->app->response->cache($cache);
        }
        // TODO 处理options请求
        if ($this->app->request->isMethod('OPTIONS')) {
            $this->app->response->withHeader('Access-Control-Allow-Origin', '*');
        }
        if (!empty($allowOrigin = $this->allowOrigin)) {
            $origin = $this->app->request->header('origin');
            if (is_string($allowOrigin) && ('*' === $allowOrigin || ($origin && $origin == $allowOrigin))) {
                $allowed = $allowOrigin;
            } else if (is_array($allowOrigin) && $origin = $this->app->request->header('origin') && in_array($origin, $allowOrigin)) {
                $allowed = $origin;
            }
            if (isset($allowed)) {
                $this->app->response->withHeader('Access-Control-Allow-Origin', $allowed);
            }
        }
        return $this->app->middleware
            ->through($this->middleware)
            ->then(function () {
                if ($this->route instanceof \Closure) {
                    $request = function () {
                        return $this->app->invokeFunc($this->route);
                    };
                } else if (is_array($this->route) && 2 === count($this->route)) {
                    $this->app->request->setAction($this->route[1]);
                    $this->app->make($this->route[0]);
                    $request = function () {
                        return $this->app->invokeMethod($this->route, $this->app->request->routeParams());
                    };
                } else {
                    throw new \Exception('Cannot resolve request');
                }
                return $this->app->middleware->then($request)->end();
            })->end();
    }

}
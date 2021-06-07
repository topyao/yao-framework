<?php
declare(strict_types=1);

namespace Max\Http;

use Max\{Exception\RouteNotFoundException, Foundation\App, Foundation\Config, Lang\Lang};
use Max\Http\Route\Cors;

/**
 * @method $this get(string $path, mixed $location)
 * @method $this post(string $path, mixed $location)
 * @method $this delete(string $path, mixed $location)
 * @method $this put(string $path, mixed $location)
 * @method $this patch(string $path, mixed $location)
 * 路由操作类
 * Class Route
 * @package Max
 */
class Route
{

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
     * Config配置实例
     * @var Config
     */
    protected $config;

    /**
     * 响应实例
     * @var Response
     */
    protected $response;

    /**
     * 已注册标识
     * @var bool
     */
    protected $registered = false;

    /**
     * 多语言
     * @var Lang
     */
    protected $lang;

    /**
     * 路由注册树
     * @var array
     */
    protected $routesMap = [
        'get'    => [],
        'post'   => [],
        'put'    => [],
        'delete' => [],
        'patch'  => [],
        'head'   => []
    ];

    /**
     * 注册路由的方法
     * @var string|array
     */
    protected $method;

    protected $controller;

    /**
     * 注册路由的path
     * @var string
     */
    protected $path = '';

    protected $namespace;

    /**
     * 路由注册的地址
     * @var
     */
    protected $location;

    protected $middleware = '';

    protected $ext = '';

    /**
     * 初始化实例列表
     * Route constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app      = $app;
        $this->request  = $app['request'];
        $this->config   = $app['config'];
        $this->response = $app['response'];
        $this->lang     = $app['lang'];
    }

    /**
     * 路由注册
     * @param $method
     * @param $arguments
     * @return $this
     */
    public function __call($method, $arguments)
    {
        if (isset($this->routesMap[$method])) {
            $this->setRoute($method, ...$arguments);
            return $this;
        }
        throw new RouteNotFoundException('Method Not Allowed: ' . $method);
    }

    /**
     * 重定向路由
     * @param string $uri
     * @param string $location
     * @param int $code
     * @param array|string[] $methods
     * @return $this
     */
    public function redirect(string $uri, string $location, int $code = 302, array $methods = ['get'])
    {
        //可以让路由传递参数给闭包
        $this->setRoute($methods, $uri, function () use ($code, $location) {
            return redirect($location, $code);
        });
        return $this;
    }

    /**
     * 视图路由
     * @param string $uri
     * @param string $view
     * @param array $data
     * @param array|string[] $methods
     * @return $this
     */
    public function view(string $uri, string $view, array $data = [], array $methods = ['get'])
    {
        $this->setRoute($methods, $uri, function () use ($view, $data) {
            return view($view, $data);
        });
        return $this;
    }

    /**
     * @param string $uri
     * @param $location
     * @param array|string[] $requestMethods
     * @return $this
     */
    public function rule(string $uri, $location, array $requestMethods = ['get', 'post']): Route
    {
        $this->setRoute($requestMethods, $uri, $location);
        return $this;
    }

    /**
     * 返回已注册标识
     * @return bool
     */
    public function isRegistered(): bool
    {
        return $this->registered;
    }

    /**
     * 未匹配到路由
     * @param \Closure $closure
     * @param array $data
     * @return $this
     */
    public function none(\Closure $closure, $data = [])
    {
        $this->routesMap['none'] = [
            'route' => $closure,
            'data'  => $data
        ];
        return $this;
    }

    /**
     * 路由中间件注册方法
     * @param string|array $middleware
     * 中间件完整类名字符串或者索引数组
     * @return $this
     */
    public function middleware($middleware)
    {
        $this->routesMap[$this->method][$this->path]['middleware'] = $middleware;
        return $this;
        foreach ((array)$this->method as $method) {
            if ($this->request->isMethod($method) && $this->request->is($this->path)) {
                $this->app['middleware']->through($middleware);
            }
        }
        return $this;
    }

    /**
     * 后缀[debug]暂时不可用
     * @param string $ext
     * @return $this
     */
    public function ext(string $ext)
    {
        foreach ((array)$this->method as $method) {
            $this->routesMap[$method][$this->path]['ext'] = $ext;
        }
        return $this;
    }

    /**
     * 路由别名设置
     * @param string $name
     * 路由别名
     * @return $this
     */
    public function alias(string $name): Route
    {
        $this->app['alias']->set($name, $this->path);
        return $this;
    }

    public function group($name, \Closure $group)
    {
        foreach ($name as $k => $v) {
            $this->$k = $v;
        }
        $group($this);
        foreach ($name as $k => $v) {
            $this->$k = '';
        }
    }

    /**
     * 路由允许跨域设置
     * @param string|array $allowOrigin
     * 允许跨域域名
     * @param string $allowCredentials
     * 是否可以将对请求的响应暴露给页面
     * @param string $allowHeaders
     * 允许的头信息
     * @param int $allowAge
     * 缓存预检时间
     * @return $this
     */
    public function cors($allowOrigin = '*', string $allowCredentials = 'true', string $allowHeaders = '*', int $maxAge = 600): Route
    {
        if ($this->request->is($this->path)) {
            $this->app[Cors::class]
                ->setAllowOrigin($allowOrigin)
                ->setAllowHeaders($allowHeaders)
                ->setCredentials($allowCredentials)
                ->setMaxAge($maxAge)
                ->setAllowMethod($this->request->method())
                ->allow();
        }
        return $this;
    }

    /**
     * 缓存时间/秒
     * @param int $expire
     */
    public function cache(int $expire)
    {
        if ($this->request->is($this->path)) {
            $this->response->cache($expire);
        }
        return $this;
    }

    private function setRoute($method, $uri, $location)
    {
        [$this->method, $this->path] = [$method, '/' . trim($uri, '/')];
        foreach ((array)$this->method as $method) {
            $this->routesMap[$method]["{$this->path}{$this->ext}"] = [
                'route'      => $location,
                'middleware' => $this->middleware,
                'ext'        => $this->ext,
                'controller' => $this->controller,
                'namespace'  => $this->namespace
            ];
        }
    }

    public function dispatch()
    {
        $method   = $this->request->method();
        $path     = $this->request->path();
        $dispatch = null;
        if ($this->hasRoute($method, $path)) {
            $dispatch = $this->getRoutes($method, $path);
        } else {
            foreach ($this->withMethod($method) as $uri => $location) {
                //设置路由匹配正则
                $uriRegexp = '#^' . $uri . '$#iU';
                //路由和请求一致或者匹配到正则
                if (preg_match($uriRegexp, $path, $match)) {
                    //如果是正则匹配到的uri且有参数传入则将参数传递给成员属性param
                    if (isset($match)) {
                        array_shift($match);
                        $this->request->routeParams($match);
                    }
                    $dispatch = $location['route'];
                    break;
                }
            }
        }
        if (is_null($dispatch)) {
            if (!isset($this->routesMap['none'])) {
                throw new RouteNotFoundException($this->lang->out('page not found', $this->request->path()), 404);
            }
            $this->request->routeParams($this->routesMap['none']['data']);
            $dispatch = $this->routesMap['none']['route'];
        }
        if ($dispatch instanceof \Closure) {
            $this->request->controller($dispatch);
            return $this->dispatchToRoute();
        }
        if (is_string($dispatch)) {
            $dispatch = explode('@', $dispatch, 2);
            if (!isset($dispatch[1])) {
                throw new RouteNotFoundException($this->lang->out('no action found'), 404);
            }
            [$controller, $action] = $dispatch;
            $controller = 'App\\Http\\Controllers\\' . implode('\\', array_map(function ($value) {
                    return ucfirst($value);
                }, explode('/', $controller)));
            $dispatch   = [$controller, $action];
        }
        $this->request->controller($dispatch[0]);
        $this->request->action($dispatch[1]);
        return $this->dispatchToRoute();
    }

    public function dispatchToRoute()
    {
        return $this->app->middleware->then(function () {
            if ($this->request->controller() instanceof \Closure) {
                $request = function () {
                    return $this->app->invokeFunc($this->request->controller());
                };
            } else if (is_string($this->request->controller())) {
                $controller = $this->app->make($this->request->controller());
                $request    = function () use ($controller) {
                    return $this->app->invokeMethod([$controller, $this->request->action()], $this->request->routeParams());
                };
            } else {
                throw new \Exception('Cannot resolve request');
            }
            return $this->app->middleware->then($request)->end();
        })->end();
    }

    private function hasRoute($method, $path)
    {
        return isset($this->routesMap[$method][$path]['route']);
    }

    private function getRoutes($method, $path)
    {
        return $this->routesMap[$method][$path]['route'];
    }

    private function withMethod($method)
    {
        if (!isset($this->routesMap[$method])) {
            throw new RouteNotFoundException('Method Not Allowed: ' . $method, 415);
        }
        return (array)$this->routesMap[$method];
    }

    /**
     * 获取路由列表
     * @param null $requestMethod
     * @param null $requestPath
     * @return array|mixed
     */
    public function getRoute($requestMethod = null, $requestPath = null)
    {
        return $requestPath ? $this->routesMap[$requestMethod][$requestPath] : ($requestMethod ? $this->routesMap[$requestMethod] : $this->routesMap);
    }


    /**
     * 路由注册方法
     * @return $this
     */
    public function register()
    {
        if (false === $this->registered) {
            $this->registered = true;
            if (file_exists($routes = env('storage_path') . 'cache' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'routes.php')) {
                $this->routesMap = unserialize(file_get_contents($routes));
            } else {
                $routes = $this->request->isAjax() ? ['api'] : ['web'];
                array_push($routes, 'both');
                foreach ($routes as $route) {
                    $file = env('route_path') . $route . '.php';
                    if (file_exists($file)) {
                        include $file;
                    }
                }
            }
        }
        return $this;
    }

}

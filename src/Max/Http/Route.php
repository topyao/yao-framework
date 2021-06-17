<?php
declare (strict_types=1);

namespace Max\Http;

use Max\Exception\RouteNotFoundException;
use Max\Foundation\{App, Config};
use Max\Http\Route\{Alias, Cors};

/**
 * @method $this get(string $path, string|arrray|\Closure $location) GET方式请求的路由
 * @method $this post(string $path, string|arrray|\Closure $location) POST方式请求的路由
 * @method $this delete(string $path, string|arrray|\Closure $location) DELETE方式请求的路由
 * @method $this put(string $path, string|arrray|\Closure $location) PUT方式请求的路由
 * @method $this patch(string $path, string|arrray|\Closure $location) PATCH方式请求的路由
 * 路由操作类
 * Class Route
 * @author chengyao
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
     * 路由注册树
     * @var array
     */
    protected $routesMap = [];

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

    protected $callable;

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
        $this->app     = $app;
        $this->request = $app['request'];
        $this->config  = $app['config'];
    }

    /**
     * 路由注册
     * @param $method
     * @param $arguments
     * @return $this
     */
    public function __call($method, $arguments)
    {
        $this->setRoute($method, ...$arguments);
        return $this;
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
            'data'  => $data,
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
        foreach ((array)$this->method as $method) {
            $this->routesMap[$this->method][$this->path]['middleware'] = $middleware;
        }
        //TODO 重复注册
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
        $this->app[Alias::class]->set($name, $this->path);
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
            $this->app->response->cache($expire);
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
                'namespace'  => $this->namespace,
            ];
        }
    }

    /**
     * 路由匹配
     * @return mixed
     */
    public function matched()
    {
        $method = strtolower($this->app->request->method());
        if (!isset($this->routesMap[$method])) {
            throw new RouteNotFoundException();
        }
        $path = $this->app->request->path();
        if (isset($this->routesMap[$method][$path])) {
            return $this->routesMap[$method][$path]['route'];
        }
        $routes = $this->routesMap[$method];
        foreach ($routes as $uri => $location) {
            $uriRegexp = '#^' . $uri . '$#iU';
            if (preg_match($uriRegexp, $path, $match)) {
                if (isset($match)) {
                    array_shift($match);
                    $this->request->routeParams($match);
                }
                return $location['route'];
            }
        }
        if (isset($this->routesMap['none'])) {
            return $this->routesMap['none']['route'];
        }
        throw new RouteNotFoundException();
    }

    public function dispatch()
    {
        $this->callable = $this->matched();
        if (is_string($this->callable)) {
            if ('C:' === substr($this->callable, 0, 2)) {
                $this->callable = \Opis\Closure\unserialize($this->callable);
            } else {
                $callable = explode('@', $this->callable, 2);
                if (!isset($callable[1])) {
                    throw new RouteNotFoundException('No action found.', 404);
                }
                $this->callable = ['App\\Http\\Controllers\\' . implode('\\', array_map(function ($value) {
                        return ucfirst($value);
                    }, explode('/', $callable[0]))), $callable[1]];
            }
        }
        return $this->app->middleware->then(function () {
            if ($this->callable instanceof \Closure) {
                $request = function () {
                    return $this->app->invokeFunc($this->callable);
                };
            } else if (is_array($this->callable) && 2 === count($this->callable)) {
                $this->request->setAction($this->callable[1]);
                $this->app->make($this->callable[0]);
                $request = function () {
                    return $this->app->invokeMethod($this->callable, $this->request->routeParams());
                };
            } else {
                throw new \Exception('Cannot resolve request');
            }
            return $this->app->middleware->then($request)->end();
        })->end();
    }

    /**
     * 获取路由列表
     * @param null $requestMethod
     * @param null $requestPath
     * @return array|mixed
     */
    public function all($requestMethod = null, $requestPath = null)
    {
        return $requestPath ? $this->routesMap[$requestMethod][$requestPath] : ($requestMethod ? $this->routesMap[$requestMethod] : $this->routesMap);
    }

    /**
     * 路由注册方法
     * @return $this
     * @throws \Exception
     */
    public function register()
    {
        if (file_exists($routes = env('storage_path') . 'cache' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'routes.php')) {
            $this->routesMap = unserialize(file_get_contents($routes));
        } else {
            foreach (glob(env('route_path') . '*.php') as $route) {
                include $route;
            }
        }
        return $this;
    }

}

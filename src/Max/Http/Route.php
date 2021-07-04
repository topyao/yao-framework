<?php
declare (strict_types=1);

namespace Max\Http;

use Max\{App, Config, Http\Route\Alias, Http\Route\Dispatcher};
use Max\Exception\RouteNotFoundException;

/**
 * 路由操作类
 * Class Route
 * @author chengyao
 * @package Max
 */
class Route
{

    /**
     * App
     * @var App
     */
    protected $app;

    /**
     * 路由
     * @var array
     */
    protected $routesMap = [];

    /**
     * 匹配到的路由信息
     * @var array
     */
    protected $matched = [];

    /**
     * 注册路由请求方法
     * @var array
     */
    protected $method;

    /**
     * 注册路由的path
     * @var string
     */
    protected $path = '';

    /**
     * @var \Closure|array|string
     */
    protected $callable;

    /**
     * 路由后缀
     * @var string
     */
    protected $ext = '';

    /**
     * 初始化实例列表
     * Route constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $path
     * @param $location
     * @return $this
     */
    public function patch(string $path, $location)
    {
        return $this->rule($path, $location, ['PATCH']);
    }

    /**
     * @param string $path
     * @param $location
     * @return $this
     */
    public function put(string $path, $location)
    {
        return $this->rule($path, $location, ['PUT']);
    }

    /**
     * @param string $path
     * @param $location
     * @return $this
     */
    public function delete(string $path, $location)
    {
        return $this->rule($path, $location, ['DELETE']);
    }

    /**
     * POST方式请求的路由
     * @param string $path
     * @param $location
     * @return $this
     */
    public function post(string $path, $location)
    {
        return $this->rule($path, $location, ['POST']);
    }

    /**
     * GET方式请求的路由
     * @param string $path
     * @param $location
     * @return $this
     */
    public function get(string $path, $location)
    {
        return $this->rule($path, $location, ['GET']);
    }

    /**
     * @param string $path
     * 请求路径
     * @param $location
     * 目标位置
     * @param string[] $method
     * 请求方式
     */
    public function rule(string $path, $location, $methods = ['GET', 'POST'])
    {
        [$this->method, $this->path, $this->ext] = [$methods, '/' . trim($path, '/'), ''];
        foreach ($methods as $method) {
            $this->routesMap[strtoupper($method)]["{$this->path}{$this->ext}"] = [
                'route'       => $location,
                'middleware'  => [],
                'ext'         => '',
                'cache'       => false,
                'allowOrigin' => [],
            ];
        }
        return $this;
    }

    /**
     * 重定向路由
     * @param string $path
     * @param string $location
     * @param int $code
     * @param array|string[] $methods
     * @return $this
     */
    public function redirect(string $path, string $location, int $code = 302, array $methods = ['GET'])
    {
        return $this->rule($path, function () use ($code, $location) {
            \Max\redirect($location, $code);
            exit;
        }, $methods);
    }

    /**
     * 视图路由
     * @param string $path
     * @param string $view
     * @param array $data
     * @param array|string[] $methods
     * @return $this
     */
    public function view(string $path, string $view, array $data = [], array $methods = ['GET'])
    {
        return $this->rule($path, function () use ($view, $data) {
            return \Max\view($view, $data);
        }, $methods);
    }

    /**
     * 未匹配到路由
     * @param \Closure $closure
     * @param array $data
     * @return $this
     */
    public function miss(\Closure $closure)
    {
        $this->routesMap['miss'] = $closure;
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
        foreach ($this->method as $method) {
            array_push($this->routesMap[strtoupper($method)]["{$this->path}{$this->ext}"]['middleware'], ...(array)$middleware);
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
        foreach ($this->method as $method) {
            $this->routesMap[$method][$this->path . $ext] = $this->routesMap[$method][$this->path];
            unset($this->routesMap[$method][$this->path]);
        }
        $this->path .= $ext;
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
     * 跨域支持
     * @param string|string[] $allowOrigin
     * @return $this
     */
    public function cors($allowOrigin = '*')
    {
        $this->setOption('allowOrigin', $allowOrigin);
        return $this;
    }

    /**
     * 请求缓存
     * @param int $expire
     * 缓存时间/秒
     */
    public function cache(int $expire)
    {
        $this->setOption('cache', $expire);
        return $this;
    }

    /**
     * @param string $key
     * @param $value
     */
    protected function setOption(string $key, $value)
    {
        foreach ($this->method as $method) {
            $this->routesMap[strtoupper($method)]["{$this->path}{$this->ext}"][$key] = $value;
        }
    }

    /**
     * 路由匹配
     * @return mixed
     */
    public function matched()
    {
        $method = $this->app->request->method();
        if (!isset($this->routesMap[$method])) {
            throw new RouteNotFoundException();
        }
        $path = $this->app->request->path();
        if (isset($this->routesMap[$method][$path])) {
            return $this->routesMap[$method][$path];
        }
        $routes = $this->routesMap[$method];
        foreach ($routes as $uri => $location) {
            $uriRegexp = '#^' . $uri . '$#iU';
            if (preg_match($uriRegexp, $path, $match)) {
                if (isset($match)) {
                    array_shift($match);
                    $this->app->request->routeParams($match);
                }
                return $location;
            }
        }
        if (isset($this->routesMap['miss'])) {
            return $this->routesMap['miss'];
        }
        throw new RouteNotFoundException();
    }

    public function dispatch()
    {
        return $this->app->make(Dispatcher::class, [$this->matched()])->dispatch();
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
        if (file_exists($routes = \Max\env('storage_path') . 'cache/app/routes.php')) {
            $this->routesMap = unserialize(file_get_contents($routes));
        } else {
            foreach (glob(\Max\env('route_path') . '*.php') as $route) {
                include $route;
            }
        }
        return $this;
    }

}

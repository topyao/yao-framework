<?php

declare(strict_types=1);

namespace Yao\Http;

use Yao\App;
use Yao\Config;
use Yao\Exception\RouteNotFoundException;
use Yao\Http\Request;
use Yao\Http\Response;

/**
 * 路由操作类
 * Class Route
 * @package Yao
 */
class Route
{

    /**
     * 容器实例
     * @var App
     */
    protected App $app;

    /**
     * 请求实例
     * @var mixed|object|\Yao\Http\Request
     */
    protected Request $request;

    /**
     * Config配置实例
     * @var mixed|object|Config
     */
    protected Config $config;

    /**
     * 响应实例
     * @var mixed|object|\Yao\Http\Response
     */
    protected Response $response;

    /**
     * 路由注册树
     * @var array
     */
    protected array $routes = [];

    /**
     * 当前请求的控制器
     * @var string
     */
    public string $controller = 'App\\Http\\Controllers';

    /**
     * 当前请求的方法
     * @var string
     */
    public string $action = '';

    /**
     * 路由传递的参数
     * @var array
     */
    public array $param = [];

    /**
     * 注册路由的方法
     * @var
     */
    private $method;

    /**
     * 注册路由的path
     * @var string
     */
    private string $path = '';

    /**
     * 路由注册的地址
     * @var
     */
    private $location;

    /**
     * 路由中间件
     * @var
     */
    private $middleware;

    /**
     * 初始化实例列表
     * Route constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $app['request'];
        $this->config = $app['config'];
        $this->response = $app['response'];
    }

    /**
     * 获取路由列表
     * @param null $requestMethod
     * @param null $requestPath
     * @return array|mixed
     */
    public function getRoute($requestMethod = null, $requestPath = null)
    {
        return $requestPath ? $this->routes[$requestMethod][$requestPath] : ($requestMethod ? $this->routes[$requestMethod] : $this->routes);
    }


    /**
     * 重定向路由
     * @param string $path
     * @param string $location
     * @param int $code
     * @param array|string[] $requestMethods
     */
    public function redirect(string $path, string $location, int $code = 302, array $requestMethods = ['get'])
    {
        $this->_rule($requestMethods, $path, $location, 'route', function () use ($code, $location) {
            return redirect($location, $code);
        });
        return $this;
    }

    /**
     * 未匹配到路由
     * @param \Closure $closure
     * @param array $data
     * @return $this
     */
    public function none(\Closure $closure, $data = [])
    {
        $this->routes['none'] = ['route' => $closure, 'data' => $data];
        return $this;
    }

    /**
     * 跨域支持
     */
    public function allowCors()
    {
        if (isset($this->routes[$this->request->method()][$this->request->path()]['cors'])) {
            $allows = $this->routes[$this->request->method()][$this->request->path()]['cors'];
            $origin = $allows['origin'] ?? $this->config->get('cors.origin');
            $credentials = $allows['credentials'] ?? ($this->config->get('cors.credentials') ? 'true' : 'false');
            $headers = $allows['headers'] ?? $this->config->get('cors.headers');
            $age = $allows['max_age'] ?? $this->config->get('cors.max_age');
            header('Access-Control-Allow-Origin:' . $origin);
            header('Access-Control-Allow-Credentials:' . $credentials);
            header('Access-Control-Allow-Headers:' . $headers);
            header('Access-Control-Max-Age:' . $age);
        } else if ('options' == $this->request->method()) {
            //需要优化下，解决了其他请求方式下的跨域问题
            $allows = $this->routes[$this->request->method()][$this->request->path()]['cors'];
            $origin = $allows['origin'] ?? $this->config->get('cors.origin');
            $credentials = $allows['credentials'] ?? ($this->config->get('cors.credentials') ? 'true' : 'false');
            $headers = $allows['headers'] ?? $this->config->get('cors.headers');
            $age = $allows['max_age'] ?? $this->config->get('cors.max_age');
            header('Access-Control-Allow-Origin:' . $origin);
            header('Access-Control-Allow-Credentials:' . $credentials);
            header('Access-Control-Max-Age:' . $age);
            header('Access-Control-Allow-Headers:' . $headers, true, 204);
            exit;
        }
    }

    public function match()
    {
        $this->allowCors();
        if (!array_key_exists($this->request->method(), $this->routes)) {
            throw new RouteNotFoundException('请求类型' . $this->request->method() . '没有定义任何路由', 404);
        }
        if (isset($this->routes[$this->request->method()][$this->request->path()]['route'])) {
            return $this->_locate($this->routes[$this->request->method()][$this->request->path()]['route']);
        } else {
            foreach ($this->routes[$this->request->method()] as $uri => $location) {
                //设置路由匹配正则
                $uriRegexp = '#^' . $uri . '$#iU';
                //路由和请求一致或者匹配到正则
                if (preg_match($uriRegexp, $this->request->path(), $match)) {
                    //如果是正则匹配到的uri且有参数传入则将参数传递给成员属性param
                    if (isset($match)) {
                        array_shift($match);
                        $this->param = $match;
                    }
                    return $this->_locate($location['route']);
                }
            }
        }
        if (isset($this->routes['none'])) {
            $this->param = $this->routes['none']['data'];
            return $this->_locate($this->routes['none']['route']);
        } else {
            throw new RouteNotFoundException('页面不存在！', 404);
        }
    }

    /**
     * 视图路由
     * @param string $path
     * @param string $view
     * @param array $data
     * @param array|string[] $requestMethods
     * @return $this
     */
    public function view(string $path, string $view, array $data = [], array $requestMethods = ['get'])
    {
        $this->_rule($requestMethods, $path, $view, 'route', function () use ($view, $data) {
            return view($view, $data);
        });
        return $this;
    }

    private function _locate($location)
    {
        if (is_array($location) && 2 == count($location)) {
            [$this->controller, $this->action] = $location;
        } else if (is_string($location)) {
            $controller = explode('/', $location);
            if (count($controller) < 2) {
                throw new \Exception("{$location}中的控制器不存在");
            }
            $this->action = array_pop($controller);
            foreach ($controller as $directory) {
                $this->controller .= '\\' . ucfirst($directory);
            }
        } else {
            $this->controller = $location;
        }
        return true;
    }

    public function middleware($middleware)
    {
        $this->app[\Yao\Http\Middleware::class]->set($middleware, $this->method, $this->path);
        $this->_rule($this->method, $this->path, $this->location, 'middleware', $middleware);
        return $this;
    }

    public function dispatch()
    {
        if (empty($this->controller)) {
            throw new \Exception('页面不存在！', 404);
        }
        if ($this->controller instanceof \Closure) {
            $resData = function () {
                return call_user_func_array($this->controller, $this->param);
            };
            if (isset($this->routes[$this->request->method()][$this->request->path()]['middleware'])) {
                $middleware = $this->routes[$this->request->method()][$this->request->path()]['middleware'];
                return (new $middleware)->handle($resData, function ($resData) {
                    return $this->response->data($resData)->return();
                });
            }
        } else if (is_string($this->controller)) {
            $resData = function () {
                return $this->app->invokeMethod([$this->controller, $this->action], $this->param);
            };
            if (isset($this->routes[$this->request->method()][$this->request->path()]['middleware'])) {
                $middleware = $this->routes[$this->request->method()][$this->request->path()]['middleware'];
            } else if (isset(get_class_vars($this->controller)['middleware'][$this->action])) {
                $middleware = get_class_vars($this->controller)['middleware'][$this->action];
            }
            if (isset($middleware)) {
                return (new $middleware)->handle($resData, function ($resData) {
                    return $this->response->data($resData)->return();
                });
            } else {
                return $this->response->data($resData)->return();
            }
        }
    }


    /**
     * get方式注册的路由
     * @param $uri
     * @param $location
     * @return $this
     */
    public function get($uri, $location)
    {
        $this->_rule('get', $uri, $location, 'route', $location);
        return $this;
    }

    /**
     * post方式注册的路由
     * @param $uri
     * @param $location
     * @return $this
     */
    public function post($uri, $location)
    {
        $this->_rule('post', $uri, $location, 'route', $location);
        return $this;
    }

    /**
     * put方式注册的路由
     * @param $uri
     * @param $location
     * @return $this
     */
    public function put($uri, $location)
    {
        $this->_rule('put', $uri, $location, 'route', $location);
        return $this;
    }

    /**
     * delete方式注册的路由
     * @param $uri
     * @param $location
     * @return $this
     */
    public function delete($uri, $location)
    {
        $this->_rule('delete', $uri, $location, 'route', $location);
        return $this;
    }

    /**
     * patch方式注册的路由
     * @param $uri
     * @param $location
     * @return $this
     */
    public function patch($uri, $location)
    {
        $this->_rule('patch', $uri, $location, 'route', $location);
        return $this;
    }


    private function _rule($method, $path, $location, $property, $value)
    {
        [$this->method, $this->path, $this->location] = [$method, '/' . trim($path, '/'), $location];
        foreach ((array)$this->method as $method) {
            $this->routes[strtolower($method)][$this->path][$property] = $value;
        }
    }

    /**
     * 路由别名设置
     * @param $name
     * 路由别名
     * @return $this
     */
    public function alias(string $name): Route
    {
        $this->app['alias']->set($name, $this->path);
        return $this;
    }

    /**
     * 路由允许跨域设置
     * @param null $AllowOrigin
     * 允许跨域域名
     * @param null $AllowCredentials
     * 是否可以将对请求的响应暴露给页面
     * @param null $AllowHeaders
     * 允许的头信息
     * @return $this
     */
    public function cors($allowOrigin = null, ?bool $allowCredentials = null, $allowHeaders = null, $allowAge = 600): Route
    {
        //需要判断是否存在配置，不存在则默认
        $cors = $this->config->get('cors');
        $allowOrigin || $allowOrigin = $cors['origin'];
        $allowHeaders || $allowHeaders = $cors['headers'];
        $allowAge || $allowAge = $cors['max_age'];
        isset($allowCredentials) || $allowCredentials = $cors['credentials'];
        $allowCredentials = $allowCredentials ? 'true' : 'false';
        foreach ((array)$this->method as $method) {
            $this->routes[$method][$this->path]['cors'] = [
                'origin' => $allowOrigin,
                'credentials' => $allowCredentials,
                'headers' => $allowHeaders
            ];
        }
        return $this;
    }

    /**
     * 多类型route注册
     * @param string $uri
     * 访问路径
     * @param string $location
     * 路由表达式
     * @param array $type
     * 多个请求方式的数组
     */
    public function rule(string $uri, $location, array $requestMethods = ['get', 'post']): Route
    {
        $this->_rule($requestMethods, $uri, $location, 'route', $location);
        return $this;
    }

    /**
     * 路由注册方法
     */
    public function register()
    {
        if (file_exists($routes = env('storage_path') . 'cache' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'routes.php')) {
            $this->routes = unserialize(file_get_contents($routes));
        } else {
            $files = env('routes_path') . '*' . 'php';
            array_map(
                fn($routes) => require_once($routes),
                glob($files)
            );
        }
    }
}

<?php
declare(strict_types=1);

namespace Yao\Route;

use Yao\Facade\{Config, Json, Request, Response};
use Yao\Route\Rules\Alias;

/**
 * 路由操作类
 * Class Route
 * @package Yao
 */
class Route
{

    protected array $routes = [];

    public $controller = '';
    public string $action = '';
    public array $param = [];

    private $method;
    private string $path = '';
    private $location;

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
    public function redirect(string $path, string $location, int $code = 200, array $requestMethods = ['get'])
    {
        $this->_rule($requestMethods, $path, $location, 'route', function () use ($code, $location) {
            return redirect($location, $code);
        });
        return $this;
    }

    public function none(\Closure $closure, $data = [])
    {
        $this->routes['miss'] = ['route' => $closure, 'data' => $data];
        return $this;
    }

    public function allowCors()
    {
        if (isset($this->routes[$this->method][Request::path()]['cors'])) {
            $allows = $this->routes[$this->method][Request::path()]['cors'];
            $origin = $allows['origin'] ?? Config::get('cors.origin');
            $credentials = $allows['credentials'] ?? (Config::get('cors.credentials') ? 'true' : 'false');
            $headers = $allows['headers'] ?? Config::get('cors.headers');
            header('Access-Control-Allow-Origin:' . $origin);
            header('Access-Control-Allow-Credentials:' . $credentials);
            header('Access-Control-Allow-Headers:' . $headers);
        }
    }

    public function match()
    {
        $this->method = Request::method();
        $this->allowCors();
        if (!array_key_exists($this->method, $this->routes)) {
            throw new \Exception('请求类型' . $this->method . '没有定义任何路由', 404);
        }

        if (isset($this->routes[$this->method][Request::path()]['route'])) {
            return $this->_locate($this->routes[$this->method][Request::path()]['route']);
        } else {
            foreach ($this->routes[$this->method] as $uri => $location) {
                //设置路由匹配正则
                $uriRegexp = '#^' . $uri . '$#iU';
                //路由和请求一致或者匹配到正则
                if (preg_match($uriRegexp, Request::path(), $match)) {
                    //如果是正则匹配到的uri且有参数传入则将参数传递给成员属性param
                    if (isset($match)) {
                        array_shift($match);
                        $this->param = $match;
                    }
                    return $this->_locate($location['route']);
                }
            }
        }
        if (isset($this->routes['miss'])) {
            $this->param = $this->routes['miss']['data'];
            return $this->_locate($this->routes['miss']['route']);
        } else {
            throw new \Exception('页面不存在！', 404);
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
        $this->_rule($requestMethods, $path, $view, 'route', function () use ($data) {
            return view($this->location, $data);
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
            $this->controller = 'App\\Http\\Controllers';
            foreach ($controller as $directory) {
                $this->controller .= '\\' . ucfirst($directory);
            }
        } else {
            $this->controller = $location;
        }
        return true;
    }

    public function dispatch()
    {
        if (empty($this->controller)) {
            throw new \Exception('页面不存在！', 404);
        }
        if ($this->controller instanceof \Closure) {
            $resData = call_user_func_array($this->controller, $this->param);
        } else if (is_string($this->controller)) {
            $resData = function () {
                return \Yao\Container::instance()->get($this->controller)->invoke($this->action, $this->param);
            };
            if (isset(get_class_vars($this->controller)['middleware'][$this->action])) {
                $middleware = get_class_vars($this->controller)['middleware'][$this->action];
                return (new $middleware)->handle($resData, function ($request) {
                    return $this->output($request);
                });
            }
        }
        return $this->output($resData);
    }

    public function output($data)
    {
        if (is_array($data) || $data instanceof \Yao\Collection) {
            return Json::data($data)->return();
        } else if (is_string($data)) {
            return Response::data($data)->return();
        } else if ($data instanceof \Closure) {
            return $this->output($data());
        }
    }

    /**
     * 路由注册方法
     * @param string $name
     * 请求类型
     * @param array $route
     * route参数
     */
    public function __call(string $method, array $route): Route
    {
        $this->_rule($method, $route[0], $route, 'route', $route[1]);
        return $this;
    }

    private function _rule($method, $path, $location, $property, $value)
    {
        [$this->method, $this->path, $this->location] = [$method, '/' . trim($path, '/'), $location];
        if (is_array($this->method)) {
            foreach ($this->method as $method) {
                $this->routes[strtolower($method)][$this->path][$property] = $value;
            }
        } else {
            $this->routes[strtolower($this->method)][$this->path][$property] = $value;
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
        Alias::instance()->set($name, $this->path);
        return $this;
    }

    /**
     * 路由允许跨域设置
     * @param null $AllowOrigin
     * 允许跨域域名
     * @param null $AllowCredentials
     * @param null $AllowHeaders
     * 允许的头信息
     * @return $this
     */
    public function cors($AllowOrigin = null, ?bool $AllowCredentials = null, $AllowHeaders = null): Route
    {
        $cors = Config::get('cors');
        $AllowOrigin || $AllowOrigin = $cors['origin'];
        $AllowHeaders || $AllowHeaders = $cors['headers'];
        isset($AllowCredentials) || $AllowCredentials = $cors['credentials'];
        $AllowCredentials = $AllowCredentials ? 'true' : 'false';
        if (is_array($this->method)) {
            foreach ($this->method as $method) {
                $this->routes[$method][$this->path]['cors'] = [
                    'origin' => $AllowOrigin,
                    'credentials' => $AllowCredentials,
                    'headers' => $AllowHeaders
                ];
            }
        } else {
            $this->routes[$this->method][$this->path]['cors'] = [
                'origin' => $AllowOrigin,
                'credentials' => $AllowCredentials,
                'headers' => $AllowHeaders
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

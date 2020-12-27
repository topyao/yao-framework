<?php

namespace Yao;


/**
 * Class Route
 * @package Yao
 */
class Route
{
    /**
     * @var array
     * 存放路由注册树的数组
     */
    protected array $routes = [];
    private array $alias = [];
    private array $middleware = [];

    public string $controller = '';
    public string $action = '';
    public array $param = [];

    private $method;
    private string $path = '';

    public function getRoute($requestMethod = null, $requestPath = null)
    {
        return $requestPath ? $this->routes[$requestMethod][$requestPath] : ($requestMethod ? $this->routes[$requestMethod] : $this->routes);
    }

    public function __construct()
    {
    }

    public function match()
    {
        //请求类型转为小写
        $method = \Yao\Facade\Request::method();
        if (!array_key_exists($method, $this->routes)) {
            throw new \Exception('请求类型' . $method . '暂时不支持', 403);
        }

        if (isset($this->routes[$method][\Yao\Facade\Request::path()])) {
            $this->_locate($this->routes[$method][\Yao\Facade\Request::path()]);
        } else {
            foreach ($this->routes[$method] as $uri => $location) {
                //设置路由匹配正则
                $uriRegexp = '@^' . $uri . '$@i';
                //路由和请求一致或者匹配到正则
                if (preg_match($uriRegexp, \Yao\Facade\Request::path(), $match)) {
                    //如果是正则匹配到的uri且有参数传入则将参数传递给成员属性param
                    if (isset($match)) {
                        array_shift($match);
                        $this->param = $match;
                    }
                    $this->_locate($location);
                    break;
                }
            }
        }
    }


    private function _locate($location)
    {
        if ($location instanceof \Closure) {
            return response(call_user_func_array($location, $this->param));
        } else if (is_array($location) && 2 == count($location)) {
            [$this->controller, $this->action] = $location;
        } else if (is_string($location)) {
            $module = '';
            if (strpos($location, '@')) {
                $dir = explode('@', $location);
                $module = $dir[0] . '\\';
                $location = $dir[1];
            }
            [$controller, $this->action] = explode('/', $location);
            $this->controller = 'App' . '\\' . ucfirst($module) . 'Controller' . '\\' . ucfirst($controller);
        } else {
            throw new \Exception('路由配置有问题！');
        }
    }

    public function dispatch()
    {
        if (empty($this->controller)) {
            throw new \Exception('页面不存在', 404);
        }
        //创建控制器类实例
        $obj = new $this->controller();
        if (!method_exists($obj, $this->action)) {
            throw new \Exception('控制器' . $this->controller . '中的方法' . $this->action . '不存在', 404);
        }
        return response(Container::create($this->controller, $this->action, $this->param));
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
        $this->_setMethodAndPath($method, $route[0]);
        $this->routes[$this->method][$this->path] = $route[1];
        return $this;
    }

    private function _setMethodAndPath($method, $path)
    {
        $this->method = $method;
        $this->path = '/' . trim($path, '/');
    }

    public function alias($name): Route
    {
        $this->alias[$name] = [
            'method' => $this->method,
            'path' => $this->path,
        ];
        return $this;
    }

    public function cross(): Route
    {
        $this->cross[$this->method][$this->path] = true;
        return $this;
    }

    public function middleware($middleware): Route
    {
        $this->middleware[$this->path] = [
            'method' => $this->method,
            'middleware' => $middleware,
        ];
        return $this;
    }

    /**
     * 多类型route注册
     * @param string $uri
     * 访问地址
     * @param string $location
     * 路由表达式
     * @param array $type
     * 多个请求方式的数组
     */
    public function rule(string $uri, $location, array $requestMethods = ['get', 'post']): Route
    {
        $this->_setMethodAndPath($requestMethods, $uri);
        foreach ($this->method as $method) {
            //遍历请求类型并注册路由
            $this->routes[strtolower($method)][$this->path] = $location;
        }
        return $this;
    }


//    public function restful(string $uri, $location)
//    {
//        $uri = '/'.trim($uri, '/').'/';
//        $this->routes['get'][$uri] = $location . '/index';
//        $this->routes['get'][$uri . 'create'] = $location . '/create';
//        $this->routes['post'][$uri] = $location . '/save';
//        $this->routes['get'][$uri . 'edit'] = $location . '/edit';
//        $this->routes['delete'][$uri . 'delete'] = $location . '/delete';
//        $this->routes['put'][$uri . 'edit'] = $location . '/update';
//    }
}

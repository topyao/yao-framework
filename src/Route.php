<?php

namespace Yao;

use Yao\Route\Alias;

/**
 * Class Route
 * @package Yao
 */
class Route
{
    /**
     * 存放路由注册树
     * @var array
     */
    protected array $routes = [];
    /**
     * 别名路由对象
     */
    private Alias $alias;

    public string $controller = '';
    public string $action = '';
    public array $param = [];

    private $method;
    private string $path = '';
    private $location;




    public function __construct()
    {
        $this->alias = new Alias;
    }



    public function getRoute($requestMethod = null, $requestPath = null)
    {
        return $requestPath ? $this->routes[$requestMethod][$requestPath] : ($requestMethod ? $this->routes[$requestMethod] : $this->routes);
    }

    public function match()
    {
        $method = \Yao\Facade\Request::method();
        if (\Yao\Facade\Request::isMethod('options')) {
            if (isset($this->routes[$method]['options'][\Yao\Facade\Request::path()])) {
                $cors = $this->routes[$method]['options'][\Yao\Facade\Request::path()];
                header('Access-Control-Allow-Origin:' . $cors['url']);
                header('Access-Control-Allow-Headers:' . $cors['url']);
            }
        }

        if (!array_key_exists($method, $this->routes)) {
            throw new \Exception('请求类型' . $method . '没有定义任何路由', 404);
        }

        if (isset($this->routes[$method][\Yao\Facade\Request::path()]['route'])) {
            return $this->_locate($this->routes[$method][\Yao\Facade\Request::path()]['route']);
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
                    return $this->_locate($location['route']);
                }
            }
        }
        throw new \Exception('页面不存在', 404);
    }


    public function group(array $appendix, \Closure $closure)
    {
        //        $obj = (new \ReflectionFunction($closure));
        //        dump($obj);
        ////        $closure();
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
            throw new \Exception('页面找不到了', 404);
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
        $this->_setParams($method, $route[0], $route);
        $this->_setParam('route', $route[1]);
        return $this;
    }

    private function _setParams($method, $path, $location)
    {
        [$this->method, $this->path, $this->location] = [$method, '/' . trim($path, '/'), $location];
    }

    // public function view($path, $view, $args = [])
    // {
    //     $this->_setParams('get', $path, fn () => \Yao\Facade\View::fetch($view, $args));
    //     $this->_setParam('route', $this->location);
    //     return $this;
    // }


    public function alias($name): Route
    {
        $this->alias->set($name, $this->path);
        return $this;
    }
    public function getAlias(?string $alias = null)
    {
        return $this->alias->get($alias);
    }

    private function _setParam($property, $value)
    {
        if (is_array($this->method)) {
            foreach ($this->method as $method) {
                $this->routes[strtolower($method)][$this->path][$property] = $value;
            }
        } else {
            $this->routes[strtolower($this->method)][$this->path][$property] = $value;
        }
    }

    public function cross($corsParams): Route
    {
        $this->routes['options'][$this->method][$this->path] = $corsParams;
        return $this;
    }

    public function middleware($middleware): Route
    {
        $this->_setParam('middleware', $middleware);
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
        $this->_setParams($requestMethods, $uri, $location);
        $this->_setParam('route', $location);
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


    public function register()
    {
        array_map(
            fn ($routes) => require_once($routes),
            glob(ROOT . 'route' . DIRECTORY_SEPARATOR . '*' . 'php')
        );
    }
}

<?php

namespace Yao;

use Yao\Facade\Request;

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
    public static array $get = [];
    public static array $post = [];
    public static array $put = [];
    public static array $delete = [];
    public static array $patch = [];
    public static array $options = [];
    public static array $head = [];

    public static string $controller = '';
    public static string $action = '';
    public static array $param = [];

    private string $method = '';
    private string $path = '';
    private $route;

    private static array $alias = [];
    private static array $middleware = [];


    private function _matchRoute()
    {
        //请求类型转为小写
        $method = \Yao\Facade\Request::method();
        if (!property_exists(self::class, $method)) {
            throw new \Exception('请求类型' . $method . '暂时不支持', 403);
        }

        if (isset(self::$$method[\Yao\Facade\Request::path()])) {
            $this->_locate(self::$$method[\Yao\Facade\Request::path()]);
        } else {
            foreach (self::$$method as $uri => $location) {
                //设置路由匹配正则
                $uriRegexp = '@^' . $uri . '$@i';
                //路由和请求一致或者匹配到正则
                if (preg_match($uriRegexp, \Yao\Facade\Request::path(), $match)) {
                    //如果是正则匹配到的uri且有参数传入则将参数传递给成员属性param
                    if (isset($match)) {
                        array_shift($match);
                        self::$param = $match;
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
            return response(call_user_func_array($location, self::$param));
        } else if (is_array($location) && 2 == count($location)) {
            [self::$controller, self::$action] = $location;
        } else if (is_string($location)) {
            $module = '';
            if (strpos($location, '@')) {
                $dir = explode('@', $location);
                $module = $dir[0] . '\\';
                $location = $dir[1];
            }
            [$controller, self::$action] = explode('/', $location);
            self::$controller = 'App' . '\\' . ucfirst($module) . 'Controller' . '\\' . ucfirst($controller);
        } else {
            throw new \Exception('路由配置有问题！');
        }
    }

    public function dispatch()
    {
        $this->_matchRoute();
        if (empty(self::$controller)) {
            throw new \Exception('页面不存在', 404);
        }
        //创建控制器类实例
        $obj = new self::$controller();
        if (!method_exists($obj, self::$action)) {
            throw new \Exception('控制器' . self::$controller . '中的方法' . self::$action . '不存在', 404);
        }
        //调用控制器方法并传入参数
        return response(call_user_func_array([$obj, self::$action], self::$param));
    }

    /**
     * 路由注册方法
     * @param string $name
     * 请求类型
     * @param array $route
     * route参数
     */
    public function __call(string $method, array $route)
    {
        $this->method = $method;
        $method = strtolower($method);
        $path = '/' . trim($route[0], '/');
        $this->path = $path;
        self::$$method[$path] = $route[1];
        return $this;
    }

    public function alias($alias)
    {
        self::$alias[$alias] = [
            'method' => $this->method,
            'path' => $this->path,
        ];
        return $this;
    }

    public function middleware($middleware)
    {
        self::$middleware[$middleware] = [
            'method' => $this->method,
            'path' => $this->path,
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
    public function rule(string $uri, $location, array $type = ['get', 'post']): void
    {
        $uri = '/' . trim($uri, '/');
        foreach ($type as $t) {
            $t = strtolower($t);
            //遍历请求类型并注册路由
            self::$$t[$uri] = $location;
        }
    }

    public function source(string $uri, $location)
    {
        $uri = '/' . trim($uri, '/');
        self::$get[$uri] = $location . '/index';
        self::$get[$uri . '/create'] = $location . '/create';
        self::$post[$uri] = $location . '/save';
        self::$get[$uri . '/edit'] = $location . '/edit';
        self::$delete[$uri . '/delete'] = $location . 'delete';
        self::$put[$uri . '/edit'] = $location . '/update';
    }
}

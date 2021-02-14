<?php


namespace Yao\Facade;


/**
 * @method static \Yao\Http\Route get(string $uri, mixed $location) GET方式请求的路由
 * @method static \Yao\Http\Route post(string $uri, mixed $location)
 * @method static \Yao\Http\Route put(string $uri, mixed $location)
 * @method static \Yao\Http\Route delete(string $uri, mixed $location)
 * @method static \Yao\Http\Route patch(string $uri, mixed $location)
 * @method static \Yao\Http\Route view(string $uri, mixed $location, array $arguments = [], $requestMethod = ['get'])
 * @method static \Yao\Http\Route none(\Closure $closure, $data = []) 闭包处理位找到的路由
 * @method static \Yao\Http\Route redirect(string $path, string $url, int $code = 200, array $requestMethod = ['get']) 路由重定向
 * @method static \Yao\Http\Route rule(string $uri, mixed $location, array $type = ['get', 'post'])
 * @method static \Yao\Http\Route source(string $uri, mixed $location)
 * @method static \Yao\Http\Route alias(string $alias)
 * @method static getRoute(string $requestMethod = null, string $requestPath = null)
 * Class Route
 * @package \Yao\Facade
 */
class Route extends \Yao\Facade
{

    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return 'route';
    }

}

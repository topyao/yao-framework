<?php


namespace Yao\Facade;


/**
 * @method static \Yao\Route\Route get(string $uri, mixed $location) GET方式请求的路由
 * @method static \Yao\Route\Route post(string $uri, mixed $location)
 * @method static \Yao\Route\Route put(string $uri, mixed $location)
 * @method static \Yao\Route\Route delete(string $uri, mixed $location)
 * @method static \Yao\Route\Route patch(string $uri, mixed $location)
 * @method static \Yao\Route\Route view(string $uri, mixed $location, array $arguments = [], $requestMethod = ['get'])
 * @method static \Yao\Route\Route none(\Closure $closure, $data = []) 闭包处理位找到的路由
 * @method static \Yao\Route\Route redirect(string $path, string $url, int $code = 200, array $requestMethod = ['get']) 路由重定向
 * @method static \Yao\Route\Route rule(string $uri, mixed $location, array $type = ['get', 'post'])
 * @method static \Yao\Route\Route source(string $uri, mixed $location)
 * @method static \Yao\Route\Route alias(string $alias)
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

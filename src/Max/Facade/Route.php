<?php
declare(strict_types=1);

namespace Max\Facade;

use Max\Facade;

use Max\Http\Route as HttpRoute;

/**
 * @method static HttpRoute get(string $uri, mixed $location) GET方式请求的路由
 * @method static HttpRoute post(string $uri, mixed $location)
 * @method static HttpRoute put(string $uri, mixed $location)
 * @method static HttpRoute delete(string $uri, mixed $location)
 * @method static HttpRoute patch(string $uri, mixed $location)
 * @method static group(array $args, \Closure $group)
 * @method static HttpRoute view(string $uri, mixed $location, array $arguments = [], $requestMethod = ['get'])
 * @method static HttpRoute none(\Closure $closure, $data = []) 闭包处理位找到的路由
 * @method static HttpRoute redirect(string $path, string $url, int $code = 302, array $requestMethod = ['get']) 路由重定向
 * @method static HttpRoute rule(string $uri, mixed $location, array $type = ['get', 'post'])
 * @method static HttpRoute source(string $uri, mixed $location)
 * @method static all(string $requestMethod = null, string $requestPath = null)
 * Class Route
 * @package \Max\Facade
 */
class Route extends Facade
{
    protected static function getFacadeClass()
    {
        return 'route';
    }

}

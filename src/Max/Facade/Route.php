<?php
declare(strict_types=1);

namespace Max\Facade;

/**
 * @method static \Max\Http\Route get(string $uri, mixed $location) GET方式请求的路由
 * @method static \Max\Http\Route post(string $uri, mixed $location)
 * @method static \Max\Http\Route put(string $uri, mixed $location)
 * @method static \Max\Http\Route delete(string $uri, mixed $location)
 * @method static \Max\Http\Route patch(string $uri, mixed $location)
 * @method static \Max\Http\Route\Group group(\Closure $group)
 * @method static \Max\Http\Route view(string $uri, mixed $location, array $arguments = [], $requestMethod = ['get'])
 * @method static \Max\Http\Route none(\Closure $closure, $data = []) 闭包处理位找到的路由
 * @method static \Max\Http\Route redirect(string $path, string $url, int $code = 302, array $requestMethod = ['get']) 路由重定向
 * @method static \Max\Http\Route rule(string $uri, mixed $location, array $type = ['get', 'post'])
 * @method static \Max\Http\Route source(string $uri, mixed $location)
 * @method static getRoute(string $requestMethod = null, string $requestPath = null)
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

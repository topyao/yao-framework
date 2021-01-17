<?php


namespace Yao\Facade;


/**
 * @method static \Yao\Route\Route get(string $uri, mixed $location)
 * @method static \Yao\Route\Route post(string $uri, mixed $location)
 * @method static \Yao\Route\Route put(string $uri, mixed $location)
 * @method static \Yao\Route\Route delete(string $uri, mixed $location)
 * @method static \Yao\Route\Route patch(string $uri, mixed $location)
 * @method static \Yao\Route\Route redirect(string $path, string $url,array $requestMethod = ['get'],int $code = 200) 路由重定向
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
        return \Yao\Route\Route::class;
    }
}

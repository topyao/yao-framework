<?php


namespace Yao\Facade;


/**
 * @method \Yao\Route get(string $uri, mixed $location)
 * @method \Yao\Route post(string $uri, mixed $location)
 * @method \Yao\Route put(string $uri, mixed $location)
 * @method \Yao\Route delete(string $uri, mixed $location)
 * @method \Yao\Route patch(string $uri, mixed $location)
 * @method \Yao\Route rule(string $uri, mixed $location, array $type = ['get', 'post'])
 * @method \Yao\Route source(string $uri, mixed $location)
 * @method \Yao\Route alias(string $alias)
 * @method \Yao\Route middleware(string $middleware)
 * @method \Yao\Route getRoute(string $requestMethod = null, string $requestPath = null)
 * Class Route
 * @package \Yao\Facade
 */
class Route extends \Yao\Facade
{
    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \Yao\Route::class;
    }

}
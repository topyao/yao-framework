<?php


namespace Yao\Facade;


/**
 * @method static \Yao\Route get(string $uri, mixed $location)
 * @method static \Yao\Route post(string $uri, mixed $location)
 * @method static \Yao\Route put(string $uri, mixed $location)
 * @method static \Yao\Route delete(string $uri, mixed $location)
 * @method static \Yao\Route patch(string $uri, mixed $location)
 * @method static \Yao\Route rule(string $uri, mixed $location, array $type = ['get', 'post'])
 * @method static \Yao\Route source(string $uri, mixed $location)
 * @method static \Yao\Route alias(string $alias)
 * @method static getRoute(string $requestMethod = null, string $requestPath = null)
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
<?php


namespace Yao\Facade;


/**
 * @method \Yao\Route get(string $uri, $location)
 * @method \Yao\Route post(string $uri, $location)
 * @method \Yao\Route put(string $uri, $location)
 * @method \Yao\Route delete(string $uri, $location)
 * @method \Yao\Route patch(string $uri, $location)
 * @method \Yao\Route rule(string $uri, $location, $type = ['get', 'post'])
 * @method \Yao\Route source(string $uri, $location)
 * @method \Yao\Route alias(string $alias)
 * @method \Yao\Route middleware(string $middleware)
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
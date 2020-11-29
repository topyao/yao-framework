<?php


namespace yao\facade;


/**
 * @method static get(string $uri, $location)
 * @method static post(string $uri, $location)
 * @method static put(string $uri, $location)
 * @method static delete(string $uri, $location)
 * @method static patch(string $uri, $location)
 * @method static rule(string $uri, $location, $type = ['get', 'post'])
 * @method static source(string $uri, $location)
 * @method static parse()
 * @method static dispatch()
 * Class Route
 * @package yao\facade
 */
class Route extends \yao\Facade
{
    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \yao\Route::class;
    }

}
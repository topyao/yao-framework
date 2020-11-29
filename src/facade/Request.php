<?php


namespace yao\facade;


use yao\Facade;

/**
 * @method static bool isMethod(string $method)
 * @method static string method()
 * @method static string path()
 * @method static array url()
 * @method static bool isAjax()
 * @method static array get($field = null, $default = '')
 * @method static array post($field = null, $default = '')
 * @method static array put($field = null, $default = '')
 * @method static array param($field = null, $default = '')
 * @method static array file($field = null, $default = '')
 * @method static string cookie($field = null, $default = '')
 * Class Request
 * @package yao\facade
 */
class Request extends Facade
{

    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \yao\http\Request::class;
    }
}

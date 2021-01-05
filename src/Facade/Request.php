<?php


namespace Yao\Facade;


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
 * @package Yao\Facade
 */
class Request extends \Yao\Facade
{

    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \Yao\Http\Request::class;
    }
}

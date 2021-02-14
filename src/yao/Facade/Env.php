<?php


namespace Yao\Facade;

/**
 * @method static set(string $env, mixed $value)
 * @method static string get(string $key = null, $default = null)
 * Class Env
 * @package Yao\Facade
 */
class Env extends \Yao\Facade
{

    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return 'env';
    }

}
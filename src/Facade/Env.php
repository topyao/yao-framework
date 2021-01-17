<?php


namespace Yao\Facade;


/**
 * @method static load(string $envFile = null)
 * @method static string get(string $key = null, $default = null)
 * Class Env
 * @package Yao\Facade
 */
class Env extends \Yao\Facade
{

    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \Yao\Env::class;
    }

}
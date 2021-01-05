<?php


namespace Yao\Facade;


/**
 * @method static load(string $envFile = ROOT . '.env')
 * @method string get(string $key, $default)
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
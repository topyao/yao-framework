<?php


namespace yao\facade;


use yao\Facade;


/**
 * @method static load(string $envFile = ROOT . '.env')
 * @method static get(string $key, $default)
 * Class Env
 * @package yao\facade
 */
class Env extends Facade
{

    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \yao\Env::class;
    }

}
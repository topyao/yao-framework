<?php


namespace yao\facade;


use yao\Facade;

/**
 * @method static get(string $name)
 * @method static set(string $name, $value)
 * @method static flash(string $name, $value)
 * Class Session
 * @package yao\facade
 */
class Session extends Facade
{
    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \yao\http\Session::class;
    }

}
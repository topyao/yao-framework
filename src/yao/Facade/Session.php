<?php


namespace Yao\Facade;

/**
 * @method static get(string $name)
 * @method static set(string $name, $value)
 * @method static flash(string $name, $value)
 * Class Session
 * @package Yao\Facade
 */
class Session extends \Yao\Facade
{
    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return 'session';
    }

}
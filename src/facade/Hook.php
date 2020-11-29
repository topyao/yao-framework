<?php


namespace yao\facade;


use yao\Facade;

/**
 * @method mixed hook($hookClass, $args)
 * Class Hook
 * @package yao\facade
 */
class Hook extends Facade
{

    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \yao\hook\Hook::class;
    }


}
<?php

namespace yao\facade;

use yao\Facade;

/**
 * @method static get(string $key)
 * Class Config
 * @package yao\facade
 */
class Config extends Facade
{

    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \yao\Config::class;
    }
}

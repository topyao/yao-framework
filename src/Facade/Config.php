<?php

namespace Yao\Facade;

/**
 * @method static get(string $key)
 * Class Config
 * @package Yao\Facade
 */
class Config extends Yao\Facade
{

    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \Yao\Config::class;
    }
}

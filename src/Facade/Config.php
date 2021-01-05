<?php

namespace Yao\Facade;

/**
 * @method static get(string $key = null, $default = null)
 * @method static load(string $config)
 * Class Config
 * @package Yao\Facade
 */
class Config extends \Yao\Facade
{

    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \Yao\Config::class;
    }
}

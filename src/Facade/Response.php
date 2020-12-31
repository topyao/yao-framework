<?php

namespace Yao\Facade;

/**
 * @method static get(string $key = null, $default = null)
 * Class Config
 * @package Yao\Facade
 */
class Response extends \Yao\Facade
{

    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \Yao\Http\Response::class;
    }
}

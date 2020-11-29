<?php

namespace yao\facade;

use yao\Facade;

/**
 * @method static fetch(string $template, $params = [])
 * Class View
 * @package yao\facade
 */
class View extends Facade
{
    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \yao\View::class;
    }

}
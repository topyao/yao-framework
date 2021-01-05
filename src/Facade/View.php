<?php

namespace Yao\Facade;

/**
 * @method static fetch(string $template, $params = [])
 * Class View
 * @package Yao\Facade
 */
class View extends \Yao\Facade
{
    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \Yao\View::class;
    }

}
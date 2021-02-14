<?php

namespace Yao\Facade;

/**
 * @method static render(string $template, $params = [])
 * Class View
 * @package Yao\Facade
 */
class View extends \Yao\Facade
{
    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \Yao\View\Render::class;
    }

}
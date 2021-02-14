<?php

namespace Yao\Facade;

/**
 * Class Json
 * @package Yao\Facade
 * @method static \Yao\Http\Response\Json data($data)
 */
class Json extends \Yao\Facade
{
    protected static function getFacadeClass()
    {
        return \Yao\Http\Response\Json::class;
    }
}

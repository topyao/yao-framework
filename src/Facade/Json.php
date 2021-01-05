<?php

namespace Yao\Facade;

class Json extends \Yao\Facade
{
    protected static function getFacadeClass()
    {
        return \Yao\Http\Response\Json::class;
    }
}

<?php

namespace Yao\Facade;

class Provider extends \Yao\Facade
{
    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \App\Http\Provider::class;
    }

}
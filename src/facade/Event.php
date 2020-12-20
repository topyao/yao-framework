<?php

namespace Yao\Facade;

class Event extends \Yao\Facade
{
    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \App\Http\Event::class;
    }

}
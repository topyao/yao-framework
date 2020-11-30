<?php


namespace yao\facade;


class Event extends \yao\Facade
{
    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \app\http\Event::class;
    }

}
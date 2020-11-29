<?php


namespace yao\facade;


class Service extends \yao\Facade
{
    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return \app\http\Service::class;
    }

}
<?php


namespace yao\Facade;

/**
 * @method static write($logName, $message, $level, array $context = [])
 * Class Log
 * @package yao\Facade
 */
class Log extends \yao\Facade
{
    protected static function getFacadeClass()
    {
        return \yao\Log::class;
    }

}
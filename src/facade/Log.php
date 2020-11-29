<?php


namespace yao\facade;

/**
 * @method static write($logName, $message, $level, array $context = [])
 * Class Log
 * @package yao\facade
 */
class Log extends \yao\Facade
{
    protected static function getFacadeClass()
    {
        return \yao\Log::class;
    }

}
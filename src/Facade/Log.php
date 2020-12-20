<?php


namespace yao\Facade;

/**
 * @method static write($logName, $message, $level, array $context = [])
 * Class Log
 * @package yao\Facade
 */
class Log extends \Yao\Facade
{
    protected static function getFacadeClass()
    {
        return \Yao\Log::class;
    }

}
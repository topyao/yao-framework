<?php
declare(strict_types=1);

namespace Max\Facade;

use Max\Facade;

/**
 * @method static write($level, $message, array $context = [])
 * @method static emergency($message, array $context = [])
 * @method static alert($message, array $context = [])
 * @method static critical($message, array $context = [])
 * @method static error($message, array $context = [])
 * @method static warning($message, array $context = [])
 * @method static notice($message, array $context = [])
 * @method static info($message, array $context = [])
 * @method static debug($message, array $context = [])
 * Class Log
 * @package Max\Facade
 */
class Log extends Facade
{
    protected static function getFacadeClass()
    {
        return 'log';
    }

}
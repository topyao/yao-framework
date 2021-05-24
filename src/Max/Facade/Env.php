<?php
declare(strict_types=1);

namespace Max\Facade;

/**
 * @method static set(string $env, mixed $value)
 * @method static string get(string $key = null, $default = null)
 * Class Env
 * @package Max\Facade
 */
class Env extends Facade
{

    protected static function getFacadeClass()
    {
        return 'env';
    }

}
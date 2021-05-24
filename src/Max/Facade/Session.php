<?php
declare(strict_types=1);

namespace Max\Facade;

/**
 * @method static get(string $name)
 * @method static set(string $name, $value)
 * @method static has(string $name)
 * @method static flash(string $name, $value)
 * @method static destroy() 销毁
 * Class Session
 * @package Max\Facade
 */
class Session extends Facade
{

    protected static function getFacadeClass()
    {
        return 'session';
    }

}
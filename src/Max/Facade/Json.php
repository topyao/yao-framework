<?php
declare(strict_types=1);

namespace Max\Facade;

/**
 * Class Json
 * @package Max\Facade
 * @method static \Max\Http\Response\Json data($data)
 */
class Json extends Facade
{
    protected static function getFacadeClass()
    {
        return \Max\Http\Response\Json::class;
    }
}

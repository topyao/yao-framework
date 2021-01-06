<?php


namespace Yao\Facade;


use Yao\Facade;

/**
 * Class Db
 * @package Yao\Facade
 * @method static \Yao\Db name(string $table_name)
 */
class Db extends Facade
{
    protected static function getFacadeClass()
    {
        return \Yao\Db::class;
    }

}
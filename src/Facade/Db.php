<?php


namespace Yao\Facade;

use Yao\Facade;

/**
 * Class Db
 * @package Yao\Facade
 * @method static \Yao\Db name(string $table_name)
 * @method static mixed query(string $sql, ?array $data = [], bool $all = true)
 * @method static int exec(string $sql, array $data = [])
 */
class Db extends Facade
{
    protected static function getFacadeClass()
    {
        return \Yao\Db::class;
    }

    public static function __callStatic($method, $params)
    {
        $callAble = ['name', 'query', 'exec'];
        if (in_array($method, $callAble)) {
            return call_user_func_array([static::createFacade(), $method], $params);
        } else {
            throw new \Exception("方法{$method}不允许被静态调用!");
        }
    }

}
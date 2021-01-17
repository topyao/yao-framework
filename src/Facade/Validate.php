<?php


namespace Yao\Facade;

/**
 * @method static \App\Validate rule(array $rule) 设置验证规则
 * @method static \App\Validate data(array $data) 设置验证数据
 * @method static \App\Validate notice(array $notice) 设置验证提示
 * Class Validate
 * @package Yao\Facade
 */
class Validate extends \Yao\Facade
{

    protected static function getFacadeClass()
    {
        return \App\Http\Validate::class;
    }

}

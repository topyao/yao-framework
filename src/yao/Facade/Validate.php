<?php


namespace Yao\Facade;

/**
 * @method static \App\Http\Validate rule(array $rule) 设置验证规则
 * @method static \App\Http\Validate data(array $data) 设置验证数据
 * @method static \App\Http\Validate notice(array $notice) 设置验证提示
 * Class Validate
 * @package Yao\Facade
 */
class Validate extends \Yao\Facade
{

    protected static function getFacadeClass()
    {
        return 'validate';
    }

}

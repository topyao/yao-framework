<?php


namespace yao\facade;

/**
 * @method static \yao\Validate rule(array $rule) 设置验证规则
 * @method static \yao\Validate data(array $data) 设置验证数据
 * @method static \yao\Validate notice(array $notice) 设置验证提示
 * Class Validate
 * @package yao\facade
 */
class Validate extends \yao\Facade
{

    protected static function getFacadeClass()
    {
        return \app\http\Validate::class;
    }

}
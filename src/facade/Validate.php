<?php


namespace Yao\Facade;

/**
 * @method static \Yao\Validate rule(array $rule) 设置验证规则
 * @method static \Yao\Validate data(array $data) 设置验证数据
 * @method static \Yao\Validate notice(array $notice) 设置验证提示
 * Class Validate
 * @package Yao\Facade
 */
class Validate extends \Yao\Facade
{

    protected static function getFacadeClass()
    {
        return \app\http\Validate::class;
    }

}
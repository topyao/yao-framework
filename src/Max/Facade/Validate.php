<?php
declare(strict_types=1);

namespace Max\Facade;

/**
 * @method static \App\Http\Validate rule(array $rule) 设置验证规则
 * @method static \App\Http\Validate data(array $data) 设置验证数据
 * @method static \App\Http\Validate notice(array $notice) 设置验证提示
 * Class Validate
 * @package Max\Facade
 */
class Validate extends Facade
{

    protected static $singleInstance = false;

    protected static function getFacadeClass()
    {
        return 'validate';
    }

}

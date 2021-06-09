<?php
declare(strict_types=1);

namespace Max\Facade;

use App\Http\Validator;

/**
 * @method static Validator rule(array $rule) 设置验证规则
 * @method static Validator data(array $data) 设置验证数据
 * @method static Validator notice(array $notice) 设置验证提示
 * Class Validate
 * @package Max\Facade
 */
class Validate extends Facade
{

    protected static $renew = true;

    protected static function getFacadeClass()
    {
        return 'validate';
    }

}

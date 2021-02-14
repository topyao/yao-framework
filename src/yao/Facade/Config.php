<?php

namespace Yao\Facade;

/**
 * @method static get(string $key = null, $default = null) 获取配置
 * @method static load(string $config) 加载配置文件
 * @method static getByType(string $type) 按照type获取配置
 * Class Config
 * @package Yao\Facade
 */
class Config extends \Yao\Facade
{

    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return 'config';
    }
}

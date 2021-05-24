<?php
declare(strict_types=1);

namespace Max\Facade;

/**
 * @method static bool isMethod(string $method) 请求方式判断
 * @method static string method() 当前的请求方式
 * @method static string path() 请求的路径
 * @method static string url(bool $full = null)  请求的地址
 * @method static bool isAjax() 判断是否ajax请求
 * @method static string|array server(string $name = null) 获取$_SERVER
 * @method static string|array header(string $header = null) 获取header
 * @method static string ip()
 * @method static array|string|null get($field = null, $default = '') 获取GET的参数
 * @method static array|string|null post($field = null, $default = '') 获取POST的参数
 * @method static string|false raw() 获取提交的原始数据
 * @method static array|string|null param($field = null, $default = '') 获取所有参数
 * @method static array|string|null file($field = null, $default = '')
 * @method static string cookie($field = null, $default = '')
 * Class Request
 * @package Max\Facade
 */
class Request extends Facade
{
    protected static function getFacadeClass()
    {
        return 'request';
    }
}

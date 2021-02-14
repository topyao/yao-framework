<?php


namespace Yao\Facade;


/**
 * @method static bool isMethod(string $method) 请求方式判断
 * @method static string method() 当前的请求方式
 * @method static string path() 请求的路径
 * @method static string url()  请求的地址
 * @method static bool isAjax() 判断是否ajax请求
 * @method static server(?string $name = null) 获取$_SERVER
 * @method static header(?string $header = null) 获取header
 * @method static array|string|null get($field = null, $default = '') 获取GET的参数
 * @method static array|string|null post($field = null, $default = '') 获取POST的参数
 * @method static array|string|null put($field = null, $default = '') 获取PUT的参数
 * @method static array|string|null param($field = null, $default = '') 获取所有参数
 * @method static array|string|null file($field = null, $default = '')
 * @method static string cookie($field = null, $default = '')
 * Class Request
 * @package Yao\Facade
 */
class Request extends \Yao\Facade
{
    protected static $singleInstance = true;

    protected static function getFacadeClass()
    {
        return 'request';
    }
}

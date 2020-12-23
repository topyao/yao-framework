<?php

/*
 * 内置函数
*/

use Yao\Facade\Session;

if (!function_exists('abort')) {
    function abort($message, $code = 0, $class = \Exception::class, $options = null)
    {
        throw new $class($message, $code, $options);
    }
}

if (!function_exists('config')) {
    /**
     *配置文件获取辅助函数
     * @param $key
     * 配置文件名
     * @return mixed
     */
    function config(string $key = '')
    {
        return Yao\Facade\Config::get($key);
    }
}


if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        return \Yao\Facade\Env::get($key, $default);
    }
}

if (!function_exists('request')) {
    function request()
    {
        return \Yao\Facade\Request::instance();
    }
}


if (!function_exists('view')) {
    /**
     * 视图赋值和渲染方法
     * @param string $template
     * 模板名，例如index@index对应index模块的index.html文件
     * @param array $param 需要渲染给模板的变量
     * @return mixed
     */
    function view(?string $template = '', array $params = [])
    {
        return \Yao\Facade\View::fetch($template, $params);
    }
}

if (!function_exists('db')) {
    /**
     * Db类助手函数
     * @param string $tableName
     * @return \Yao\Db
     */
    function db(string $tableName): \Yao\Db
    {
        return \Yao\Db::name($tableName);
    }
}

if (!function_exists('response')) {
    function response($response)
    {
        if (is_array($response)) {
            header("Content-Type:text/json;charset=UTF-8");
            exit(json_encode($response, 256));
        } else if (is_string($response)) {
            exit($response);
        }
    }
}


function json($args)
{
}

function session($field, $value = null)
{
    if (!isset($value)) {
        return Session::get($field);
    } else {
        Session::set($field, $value);
    }
}

function redirect($url)
{
    header('location:' . $url);
    exit;
}


if (false === function_exists('getMultidimensionalArrayValue')) {
    function getMultidimensionalArrayValue($value, $string, $default = null)
    {
        $field = explode('.', $string);
        foreach ($field as $v) {
            if (isset($value[$v])) {
                $value = $value[$v];
            } else {
                $value = $default;
                break;
            }
        }
        return $value;
    }
}

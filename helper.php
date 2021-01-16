<?php

/*
 * 内置函数
*/

use Yao\Facade\Json;
use Yao\Facade\Response;
use Yao\Facade\Session;

if (false === function_exists('abort')) {
    function abort($message, $code = 0, $class = \Exception::class, $options = null)
    {
        throw new $class($message, $code, $options);
    }
}

if (false === function_exists('config')) {
    /**
     *配置文件获取辅助函数
     * @param $key
     * 配置文件名
     * @return mixed
     */
    function config(?string $key = null, $default = null)
    {
        return \Yao\Facade\Config::get($key, $default);
    }
}


if (false === function_exists('env')) {
    function env(string $key = null, $default = null)
    {
        return \Yao\Facade\Env::get($key, $default);
    }
}

if (false === function_exists('request')) {
    function request()
    {
        return \Yao\Facade\Request::instance();
    }
}


if (false === function_exists('view')) {
    /**
     * 视图赋值和渲染方法
     * @param string $template
     * 模板名，例如index@index对应index模块的index.html文件
     * @param array $params 需要渲染给模板的变量
     * @return mixed
     */
    function view(?string $template = '', array $params = [])
    {
        return \Yao\Facade\View::render($template, $params);
    }
}

if (false === function_exists('db')) {
    /**
     * Db类助手函数
     * @param string $tableName
     * @return \Yao\Db
     */
    function db(string $tableName)
    {
        return \Yao\Facade\Db::name($tableName);
    }
}
if (false === function_exists('dump')) {
    function dump(...$dump)
    {
        echo '<pre style="font-size:1.3em">';
        foreach ($dump as $d) {
            var_dump($d);
        }
        exit('</pre>');
    }
}

if (false === function_exists('json')) {
    function json($data)
    {
        return Json::data($data);
    }
}

if (false === function_exists('response')) {
    function response($data)
    {
        return Response::data($data);
    }
}
if (false === function_exists('session')) {
    function session($field, $value = null)
    {
        if (!isset($value)) {
            return Session::get($field);
        } else {
            Session::set($field, $value);
        }
    }
}

if (false === function_exists('redirect')) {
    function redirect($url, int $code = 302)
    {
        http_response_code($code);
        header('location:' . $url);
        exit;
    }
}
if (false === function_exists('url')) {
    function url($alias, $args = [])
    {
        return \Yao\Route\Rules\Alias::instance()->get($alias, $args);
    }
}

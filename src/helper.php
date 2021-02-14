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
    function json($data, ?int $code = null, $header = null)
    {
        return Json::data($data)->code($code)
            ->header($header)
            ->return();
    }
}

if (false === function_exists('response')) {
    /**
     * 响应
     * @param $data
     * 数据
     * @param int|null $code
     * 状态码
     * @param array|string|null $header
     * 头信息
     */
    function response($data, ?int $code = null, $header = null)
    {
        return Response::data($data)
            ->code($code)
            ->header($header)
            ->return();
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
        header('location:' . $url, true, $code);
        ob_end_flush();
        exit;
    }
}

if (false === function_exists('url')) {
    function url(string $alias, array $args = []): string
    {
        return \Yao\App::instance()->invokeMethod(['alias', 'get'], [$alias, $args]);
    }
}

if (false === function_exists('app')) {
    function app($app, $arguments = [], $singleInstance = false)
    {
        return \Yao\App::instance()->make($app, $arguments, $singleInstance);
    }
}

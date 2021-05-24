<?php

/*
 * 内置函数
 */

use Max\Foundation\App;

if (false === function_exists('app')) {
    /**
     * 容器实例话和获取实例
     * @param ?string|null $id
     * @param array $arguments
     * @param bool $singleInstance
     * @return mixed|object
     */
    function app(string $id = null, array $arguments = [], bool $singleInstance = true)
    {
        if (is_null($id)) {
            return App::instance();
        }
        return App::instance()->make($id, $arguments, $singleInstance);
    }
}

if (false === function_exists('invoke')) {
    /**
     * 容器调用方法
     * @param array|Closure $callable
     * 数组或者闭包
     * @param array $arguments
     * 给方法传递的参数列表
     * @param bool $singleInstance
     * 单例，仅$callable为数组时候生效
     * @param array $constructorParameters
     * 构造函数参数数组，仅$callable为数组时候生效
     * @return mixed
     * @throws Exception
     */
    function invoke($callable, array $arguments = [], bool $singleInstance = true, array $constructorParameters = [])
    {
        if (is_array($callable)) {
            return app()->invokeMethod($callable, $arguments, $singleInstance, $constructorParameters);
        }
        if ($callable instanceof Closure) {
            return app()->invokeFunc($callable, $arguments);
        }
    }
}

if (false === function_exists('abort')) {
    /**
     * @param string|array $message
     * 抛出异常的信息,支持数组，会以json形式抛出
     * @param int $code
     * @param string $class
     * @param null $options
     */
    function abort($message, $code = 0, $class = \Exception::class, $options = null)
    {
        if (is_array($message)) {
            $message = json_encode($message, JSON_UNESCAPED_UNICODE);
        }
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
    function config(string $key = null, $default = null)
    {
        return invoke(['config', 'get'], [$key, $default]);
    }
}

if (false === function_exists('env')) {
    /**
     * env获取
     * @param string|null $key
     * @param null $default
     * @return mixed
     * @throws Exception
     */
    function env(string $key = null, $default = null)
    {
        return invoke(['env', 'get'], [$key, $default]);
    }
}

if (false === function_exists('halt')) {
    /**
     * 中断调试
     * @param mixed ...$arguments
     * 多个参数
     * @return mixed
     */
    function halt(...$arguments)
    {
        echo '<title>调试信息</title>
<meta name="viewport"  content="width=device-width, initial-scale=1.0">
<style>
    .content{
        border:1px solid #d5d1d1;
        width:70vw;
        margin: .5em auto
    }

    .title{
        background-color: #1E90FF;
        line-height:3em;
        padding:0 1em;
        min-height: 3em;
        color: white;
        font-weight: bold;
        word-break: break-all;
    }
    pre{
        margin-top:0;
        padding:0 1em;
        font-size: 1.5em;
        display: block;
        word-break: break-all;
        white-space:break-spaces
    }

    @media screen and (max-width: 500px){
        .content{
            width:95vw !important;
        }
    }
</style>

<body>
<div class="content">
<div class="title">调试信息</div>
<pre style="padding-top: 1em">';
        var_dump(...$arguments);
        $timeCost    = microtime(true) - APP_START_TIME;
        $memoryUsage = (memory_get_usage() - APP_START_MEMORY_USAGE) / 1024 / 1024;
        echo '</pre><div class="title" style="display: flex;justify-content: space-between"><div id="status">运行时间：' . round($timeCost, 3) . 'S 内存消耗：' . round($memoryUsage, 3) . 'MB QPS: ' . round(1 / $timeCost, 3) . ' fetches/sec </div><div>Max&nbsp;&nbsp;<a href="https://github.com/topyao/max">Github</a>&nbsp;&nbsp<a href="https://packagist.org/packages/max/max">Packagist</a></div></div></div></body>';
        exit(app('response')->send());
    }
}

if (false === function_exists('json')) {
    /**
     * 数组转json
     * @param array $array
     * @return string
     * @throws Exception
     */
    function json(array $array): string
    {
        try {
            // 返回JSON数据格式到客户端 包含状态信息
            $json = json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (false === $json) {
                throw new \Exception(json_last_error_msg());
            }
        } catch (\Exception $e) {
            if ($e->getPrevious()) {
                throw $e->getPrevious();
            }
            throw $e;
        }
        return $json;
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
    function response($data = '', int $code = 200, array $header = [])
    {
        return app('response')->body($data)
            ->withHeader($header)
            ->withStatus($code)
            ->send();
    }
}

if (false === function_exists('session')) {

    /**
     * @param $field
     * @param null $value
     * @return mixed
     */
    function session($field, $value = null)
    {
        $session = app('session');
        if (!isset($value)) {
            return $session->get($field);
        }
        $session->set($field, $value);
    }
}

if (false === function_exists('redirect')) {
    function redirect(string $url, int $code = 302)
    {
        return invoke(['response', 'redirect'], [$url, $code]);
    }
}

if (false === function_exists('url')) {
    function url(string $alias, array $args = []): string
    {
        return invoke(['alias', 'get'], [$alias, $args]);
    }
}

if (false === function_exists('csrf')) {

    function csrf()
    {
        App::instance()->make('session')->set('token', md5(uniqid()));
    }
}

if (false === function_exists('apache_request_headers')) {
    /**
     * 兼容CLI的获取Headers的方法
     * @return array
     */
    function apache_request_headers(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $server) {
            if ('HTTP_' == substr($key, 0, 5)) {
                $headers[str_replace('_', '-', substr($key, 5))] = $server;
            }
        }
        return $headers;
    }
}

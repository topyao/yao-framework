<?php

namespace Yao;

use Yao\Facade\{
    Log,
    Response
};

/**
 * 错误和异常注册类
 * Class Error
 * @package Yao
 */
class Error
{

    protected bool $debug;
    protected string $exceptionView;


    public function __construct(App $app)
    {
        $this->app = $app;
        $this->debug = $this->app['config']->get('app.debug');
        $iniSet = [
            [true => 'On', false => 'Off'],
            [true => E_ALL, false => 0]
        ];
        function_exists('ini_set') && ini_set('display_errors', $iniSet[0][$this->debug]);
        error_reporting($iniSet[1][$this->debug]);
        $this->exceptionView = $this->app['config']->get('app.exception_view') ?: dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Tpl' . DIRECTORY_SEPARATOR . 'exception.html';
    }

    public function register()
    {
        set_error_handler([$this, 'error']);
        set_exception_handler([$this, 'exception']);
        register_shutdown_function([$this, 'shutdown']);
    }

    public function exception($exception)
    {
        $code = $exception->getCode() ?: 'Exception';
        $message = $exception->getMessage();
        Log::write('system', $message, 'notice', ['Method' => $this->app['request']->method(), 'Path' => $this->app['request']->path()]);
        if ($this->debug) {
            $data = '<!DOCTYPE html>
            <html lang="zh">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>' . $message . '</title>
            </head>
            <body>
            <b>Message:</b> ' . $message . '
             <br><b>Code:</b>' . $code . '<br><b> File:</b> ' . $exception->getFile() . '<br ><b > Line:</b > ' . $exception->getLine() . '<pre style = "font-size:1.4em;margin-top: .5em" >' . $exception->getTraceAsString() . '</pre>
            </body >
            </html > ';
        } else {
            $data = include_once $this->exceptionView;
        }
        exit($this->app['response']->data($data)->code((int)$exception->getCode())->return());
    }

    public function error($code, $message, $file, $line, $errContext)
    {
        Log::write('system', $message, 'notice', ['Method' => $this->app['request']->method(), 'Path' => $this->app['request']->path(), $code, $file, $line]);
        if ($this->debug) {
            $data = ' <title>' .$message . '</title><b>Message:</b> ' . $message . ' <br><b>Code:</b>' . $code . ' <br><b>Location:</b> ' . $file . ' + ' . $line . '<br><b>Trace:</b><br>' . print_r($errContext, true);
        } else {
            $data = include_once $this->exceptionView;
        }
        exit($this->app['response']->data($data)->code(403)->return());
    }

    public function shutdown()
    {
        if ($error = error_get_last()) {
            var_dump($error);
        }
    }
}

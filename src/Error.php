<?php

namespace Yao;

use Yao\Facade\{
    Log,
    Config,
    Response
};
use Yao\Traits\SingleInstance;

/**
 * 错误和异常注册类
 * Class Error
 * @package Yao
 */
class Error
{

    use SingleInstance;

    protected bool $debug;
    protected string $exceptionView;

    public static function register()
    {
        set_error_handler([self::instance(), 'error']);
        set_exception_handler([self::$instance, 'exception']);
        // register_shutdown_function([self::$instance, 'shutdown']);
    }

    private function __construct()
    {
        $this->debug = Config::get('app.debug');
        $iniSet = [
            [true => 'On', false => 'Off'],
            [true => E_ALL, false => 0]
        ];
        function_exists('ini_set') && ini_set('display_errors', $iniSet[0][$this->debug]);
        error_reporting($iniSet[1][$this->debug]);
        $this->exceptionView = Config::get('app.exception_view') ?: dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Tpl' . DIRECTORY_SEPARATOR . 'exception.html';
    }

    public function exception($exception)
    {
        $code = $exception->getCode() ?: 'Exception';
        $message = $exception->getMessage();
        Log::write('system', $message, 'notice');
        http_response_code((int)$exception->getCode());
        if ($this->debug) {
            return Response::data('<!DOCTYPE html>
            <html lang="zh">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>' . $message . '</title>
            </head>
            
            <style>
                body {
                    margin: 0;
                    padding: 0;
                }
            
                .container {
                    border-radius: 3px 3px 0 0;
                    width: 50vw;
                    height: 50vh;
                    background-color: rgb(255, 255, 255);
                    margin: 25vh auto;
                }
            
                .title {
                    color:#3c3c3c;
                    border-radius: 3px 3px 0 0;
                    height: 1em;
                    display:flex;
                    justify-content:space-between;
                    font-size: 1em;
                    padding: 0 .5em;
                    box-sizing: border-box;
                    line-height: 1em;
                }
            
                .content {
                    padding: 0 .5em;
                    height:80%;
                    
                }
                .trace{
                    font-size:1.2em;
                    color:#3c3c3c;
                    display:block;
                    word-wrap:break-word;
                    word-break:break-all;
                }
            </style>
            
            <body>
                <div class="container">
                    <div class="title">
                    <span>Message: ' . $message . '</span><span>' . $code . '</span>
                    </div>
                    <div class="content">
                    File:' . $exception->getFile() . ' +' . $exception->getLine() . '<pre class="trace">' . $exception->getTraceAsString() . '</pre>
                    </div>
                </div>
            </body>
            
            </html>');
        }
        return Response::data(include_once $this->exceptionView);
    }

    public function error($code, $message, $file, $line, $errContext)
    {
        Log::write('system', $message, 'notice', [$code, $file, $line]);
        if ($this->debug) {
            return Response::data(' <title>' . $message . ' </title><pre style = "font-size:1.6em" ><b>Message:</b>' . $message . ' <br><b>Code:</b>' . $code . ' <br><b>Location:</b>' . $file . '+' . $line . '</pre>');
        }
        return Response::data(include_once $this->exceptionView);
    }

    // public function shutdown()
    // {
    // }
}

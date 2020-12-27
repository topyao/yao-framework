<?php

namespace Yao;

use Throwable;

class Error
{

    protected bool $debug;
    protected string $exceptionView;
    private $error;

    private static function _getInstance()
    {
        return new self;
    }

    public static function register()
    {
        set_error_handler([self::_getInstance(), 'error']);
        set_exception_handler([self::_getInstance(), 'exception']);
        register_shutdown_function([self::_getInstance(), 'shutdown']);
    }

    private function __construct()
    {
        $this->debug = \Yao\Facade\Config::get('app.debug');
        $this->exceptionView = \Yao\Facade\Config::get('app.exception_view') ?: dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Tpl' . DIRECTORY_SEPARATOR . 'exception.html';
    }

    public function exception($exception)
    {
        $code = $exception->getCode() ?: 'Exception';
        $message = $exception->getMessage();
        \Yao\Facade\Log::write('system', $message, 'notice', ['请求地址:' . $_SERVER['REQUEST_URI'], 'trace' . $exception->getTraceAsString()]);
        http_response_code((int)$exception->getCode());
        if ($this->debug) {
            exit(' <title>' . $message . ' </title><pre style = "font-size:1.6em" >错误信息：' . $message . ' <br>错误代码：' . $code . ' <br>文件位置：' . $exception->getFile() . ' <br>错误行：' . $exception->getLine() . ' <br>Stack trace:' . $exception->getTraceAsString() . ' </pre> ');
        }
        exit(include_once $this->exceptionView);
    }

    public function error($code, $message, $file, $line, $errContext)
    {
        if ($this->debug) {
            ini_set('display_errors', 'On');
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 'Off');
            error_reporting(0);
        }
        \Yao\Facade\Log::write('system', $message, 'notice', [$code, $file, $line]);
        if ($this->debug) {
            exit(' <title>' . $message . ' </title><pre style = "font-size:1.6em" > 错误信息：' . $message . ' <br>错误代码：' . $code . ' <br>文件位置：' . $file . ' <br>错误行：' . $line . ' </pre> ');
        }
        exit(include_once $this->exceptionView);
    }

    public function shutdown()
    {
    }
}

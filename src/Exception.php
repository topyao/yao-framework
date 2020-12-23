<?php

namespace Yao;

use Throwable;

class Exception extends \Exception
{

    protected bool $debug;
    protected string $exceptionView;

    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->debug = \Yao\Facade\Config::get('app.debug');
        $this->exceptionView = \Yao\Facade\Config::get('app.exception_view');
        set_error_handler([$this, 'error']);
        set_exception_handler([$this, 'exception']);
        register_shutdown_function([$this, 'shutdown']);

    }

    public function exception($exception)
    {
        $code = $exception->getCode() ?: 'Exception';
        $message = $exception->getMessage();
        \Yao\Facade\Log::write('system', $message, 'notice', ['请求地址:' . $_SERVER['REQUEST_URI'], 'trace' . $exception->getTraceAsString()]);
        http_response_code((int)$exception->getCode());
        if ($this->debug) {
            exit('<title>' . $message . '</title><pre style="font-size:1.6em">错误信息：' . $message . '<br>错误代码：' . $code . '<br>文件位置：' . $exception->getFile() . '<br>错误行：' . $exception->getLine() . '<br>Stack trace:' . $exception->getTraceAsString() . '</pre>');
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
            exit('<title>' . $message . '</title><pre style="font-size:1.6em">错误信息：' . $message . '<br>错误代码：' . $code . '<br>文件位置：' . $file . '<br>错误行：' . $line . '</pre>');
        }
        exit(include_once $this->exceptionView);
    }

    public function shutdown()
    {

    }
}
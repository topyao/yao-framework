<?php

namespace Yao;

use Yao\Facade\{
    Log, Config
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
        register_shutdown_function([self::$instance, 'shutdown']);
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
        Log::write('system', $message, 'notice', ['请求地址:' . $_SERVER['REQUEST_URI'], 'trace' . $exception->getTraceAsString()]);
        http_response_code((int)$exception->getCode());
        if ($this->debug) {
            exit(' <title>' . $message . ' </title><style>.box{width:800px;height:4em;margin:2em auto}</style><pre class="box"><div class="title">' . $message . ' </div><div class="box2" style = "word-wrap:break-word;word-break: break-all;color:dimgrey" ><b>Code:</b>' . $code . ' <br><b>Locate:</b>' . $exception->getFile() . '&nbsp; line:' . $exception->getLine() . ' <p>Trace:</p>' . $exception->getTraceAsString() . ' </div> </pre>');
        }
        exit(include_once $this->exceptionView);
    }

    public function error($code, $message, $file, $line, $errContext)
    {
        Log::write('system', $message, 'notice', [$code, $file, $line]);
        if ($this->debug) {
            exit(' <title>' . $message . ' </title><pre style = "font-size:1.6em" ><b>Message:</b>' . $message . ' <br><b>Code:</b>' . $code . ' <br><b>Location:</b>' . $file . '+' . $line . '</pre>');
        }
        exit(include_once $this->exceptionView);
    }

    public function shutdown()
    {
    }
}

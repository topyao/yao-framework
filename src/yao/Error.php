<?php
declare(strict_types=1);

namespace Yao;

use Yao\Exception\ErrorException;
use Yao\Http\{Request, Response};

/**
 * 错误和异常注册类
 * Class Error
 * @package Yao
 */
class Error
{

    /**
     * 容器实例
     * @var App
     */
    protected App $app;

    /**
     * 请求对象
     * @var mixed|object|Request
     */
    protected Request $request;

    /**
     * 响应实例
     * @var mixed|object|Response
     */
    protected Response $response;

    /**
     * 日志实例
     * @var mixed|Log
     */
    protected Log $log;

    /**
     * 调试模式开关
     * @var bool
     */
    protected bool $debug;

    /**
     * 异常视图模板文件
     * @var array|mixed|string
     */
    protected string $exceptionView;


    /**
     * 初始化实例列表和参数
     * Error constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->log = $app['log'];
        $this->request = $app['request'];
        $this->response = $app['response'];
        $this->debug = $this->app['config']->get('app.debug');
        $this->exceptionView = $this->app->config->get('app.exception_view') ?: dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'exception.html';
    }

    /**
     * 错误和异常注册
     */
    public function register()
    {
        $iniSet = [
            [true => 'On', false => 'Off'],
            [true => E_ALL, false => 0]
        ];
        function_exists('ini_set') && ini_set('display_errors', $iniSet[0][$this->debug]);
        error_reporting($iniSet[1][$this->debug]);
        set_error_handler([$this, 'error']);
        set_exception_handler([$this, 'exception']);
        register_shutdown_function([$this, 'shutdown']);
    }

    /**
     * 异常回调函数
     * @param $exception
     */
    public function exception($exception)
    {
        $code = $exception->getCode() ?: 'Exception';
        $message = $exception->getMessage();
        $this->log->write('Exception', $message, 'notice', ['Method' => $this->request->method(), 'Path' => $this->request->path(), 'ip' => $this->request->ip()]);
        $return = $this->response;
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
             <br><b>Code:</b>' . $code . '<br><b> File:</b> ' . $exception->getFile() . '<br ><b> Line:</b> ' . $exception->getLine() . '<pre style = "font-size:1.4em;margin-top: .5em" >' . $exception->getTraceAsString() . '</pre>
            </body>
            </html> ';
            $return = $return->data($data);
        } else {
            include_once $this->exceptionView;
        }
        exit($return->code((int)$exception->getCode())->return());
    }

    /**
     * 错误回调函数
     * @param $code
     * @param $message
     * @param $file
     * @param $line
     * @param $errContext
     * @throws ErrorException
     */
    public function error($code, $message, $file, $line, $errContext)
    {
        $this->log->write('Error', $message, 'notice', ['Method' => $this->request->method(), 'Path' => $this->request->path(), 'ip' => $this->request->ip(), $code, $file, $line]);
        throw new ErrorException($message, $code);
    }

    /**
     * 脚本终止回调函数
     * @throws \Exception
     */
    public function shutdown()
    {
        if ($error = error_get_last()) {
            $this->log->write('Fetal', $error, 'notice', ['Method' => $this->request->method(), 'Path' => $this->request->path()]);
            throw new \Exception($error);
        }
    }
}

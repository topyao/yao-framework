<?php
declare(strict_types=1);

namespace Max\Foundation;

use Max\Exception\ErrorException;
use Max\Http\{Request, Response};

/**
 * 错误和异常注册类
 * Class Error
 * @package Max
 */
class Error
{

    /**
     * 容器实例
     * @var App
     */
    protected $app;

    /**
     * 请求对象
     * @var mixed|object|Request
     */
    protected $request;

    /**
     * 响应实例
     * @var mixed|object|Response
     */
    protected $response;

    /**
     * 日志实例
     * @var mixed|\Max\Log\Log
     */
    protected $log;

    /**
     * 调试模式开关
     * @var bool
     */
    protected $debug;

    /**
     * 异常视图模板文件
     * @var array|mixed|string
     */
    protected $exceptionView;


    /**
     * 初始化实例列表和参数
     * Error constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app           = $app;
        $this->log           = $app['log'];
        $this->request       = $app['request'];
        $this->response      = $app['response'];
        $this->debug         = $app['config']->get('app.debug');
        $this->exceptionView = __DIR__ . '/../Exception/view/exception.php';
        if (true === file_exists($view = $this->app->config->get('app.exception_view', ''))) {
            $this->exceptionView = $view;
        }
    }

    /**
     * 错误和异常注册
     */
    public function register()
    {
        error_reporting(E_ALL);
        set_exception_handler([$this, 'exception']);
        set_error_handler([$this, 'error']);
        register_shutdown_function([$this, 'shutdown']);
    }

    /**
     * 异常回调
     * @param \Throwable $exception
     */
    public function exception(\Throwable $exception)
    {
        [$file, $line, $message, $code] =
            [
                $exception->getFile(),
                $exception->getLine(),
                $exception->getMessage(),
                $exception->getCode() ?? '200'
            ];
        $this->log->error(
            "[{$this->request->ip()} '{$this->request->method()}': '{$this->request->url(true)}'] " . $message,
            [
                'File: ' => $file,
                'Line: ' => $line,
                'Code: ' => $code
            ]
        );
        if ($this->debug) {
            echo '<title> ', $message, '</title>
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
        white-space:break-spaces;
    }
   
    @media screen and (max-width: 500px){
        .content{
            width:95vw !important;        
        }
        #status{
            display: none;
        }
    }
</style>

<body>
<div class="content">
<div class="title">Message: ', $message, '</div>
<pre>
<p><b>File: </b>', $file, ' +', $line, '</p><p><b>Code: </b>', $code, '</p>';
            $trace = $exception->getTrace();
            for ($key = 0; $key <= count($trace) - 2; $key++) {
                if (false === isset($trace[$key]['file'])) {
                    continue;
                }
                $errorFile = $trace[$key]['file'];
                $file      = file($trace[$key]['file']);
                $line      = $trace[$key]['line'];
                $function  = $trace[$key]['function'];
                echo '<p style="background-color: #65adf3;color: white">', $errorFile, ' +', $line, '</p>';
                for ($i = $line - 4; $i < $line + 3 && $i < count($file); $i++) {
                    $code = htmlspecialchars($file[$i]);
                    echo '<span style="background-color: #EEEEEE;color: grey">', str_pad((string)($i + 1), 3, ' ', STR_PAD_BOTH), '</span>';
                    if ($i + 1 == $line) {
                        $code = '<text style="width:100%;background-color: #eeeeee">' . str_replace($function, '<span style="color: red">' . $function . '</span>', htmlspecialchars($file[$i])) . '</text>';
                    }
                    echo $code;
                }
            }
            $timeCost    = microtime(true) - APP_START_TIME;
            $memoryUsage = (memory_get_usage() - APP_START_MEMORY_USAGE) / 1024 / 1024;
            echo '</pre><div class="title" style="display: flex;justify-content: space-between"><div id="status">运行时间：' . round($timeCost, 3) . 'S 内存消耗：' . round($memoryUsage, 3) . 'MB QPS: ' . round(1 / $timeCost, 3) . ' fetches/sec </div><div>Max&nbsp;&nbsp;<a href="https://github.com/topyao/max">Github</a>&nbsp;&nbsp<a href="https://packagist.org/packages/max/max">Packagist</a></div></div></div></body>';
        } else {
            echo str_replace(['{{code}}', '{{message}}'], [$code, $message], file_get_contents($this->exceptionView));
        }
        return $this->response
            ->withStatus($code)
            ->send();
    }

    /**
     * 错误回调函数
     * @param $code
     * @param $message
     * @param $file
     * @param $line
     * @throws ErrorException
     */
    public function error(int $code, string $message, string $file = '', int $line = 0)
    {
        if (error_reporting() & $code) {
            throw new ErrorException($code, $message, $file, $line);
        }
    }

    /**
     * 脚本终止回调函数
     * @throws \Exception
     */
    public function shutdown()
    {
        if (false === is_null($error = error_get_last())) {
            throw new ErrorException($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
}
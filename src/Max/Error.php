<?php
declare(strict_types=1);

namespace Max;

use Max\Exception\ErrorException;
use Max\Exception\Handler;
use Throwable;

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
     * 初始化实例列表和参数
     * Error constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
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
     * @param Throwable $e
     */
    public function exception(Throwable $e)
    {
        $errorMsg = $this->getMessage($e);
        $status   = $this->getCode($e);
        return $this->app->response
            ->body($errorMsg)
            ->withStatus($status)
            ->send();
    }

    public function getCode($e)
    {
        return ($e instanceof \Max\Exception\HttpException) ? $e->getCode() : 500;
    }

    public function getMessage(\Throwable $e)
    {
        [$file, $line, $message, $code] =
            [
                $e->getFile(),
                $e->getLine(),
                $e->getMessage(),
                $e->getCode() ?? '200'
            ];
        $request = $this->app->request;
        $this->app->log->error(
            "[{$request->ip()} '{$request->method()}': '{$request->url(true)}'] " . $message,
            [
                'File: ' => $file,
                'Line: ' => $line,
                'Code: ' => $code
            ]
        );
        if ($this->app->config->get('app.debug')) {
            $class    = get_class($e);
            $errorMsg = <<<EOT
<title>{$message}</title>
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
<div class="title">{$class}: {$message}</div>
<pre>
<p><b>File: </b>{$file} +{$line}</p><p><b>Code: </b>{$code}</p>
EOT;
            $trace    = $e->getTrace();
            for ($key = 0; $key <= count($trace) - 2; $key++) {
                if (false === isset($trace[$key]['file'])) {
                    continue;
                }
                $errorFile = $trace[$key]['file'];
                $file      = file($trace[$key]['file']);
                $line      = $trace[$key]['line'];
                $function  = $trace[$key]['function'];
                $errorMsg  .= "<p style=\"background-color: #65adf3;color: white\">{$errorFile} +{$line}</p>";
                for ($i = $line - 4; $i < $line + 3 && $i < count($file); $i++) {
                    $code     = htmlspecialchars($file[$i]);
                    $errorMsg .= '<span style="background-color: #EEEEEE;color: grey">' . str_pad((string)($i + 1), 3, ' ', STR_PAD_BOTH) . '</span>';
                    if ($i + 1 == $line) {
                        $code = '<text style="width:100%;background-color: #eeeeee">' . str_replace($function, '<span style="color: red">' . $function . '</span>', htmlspecialchars($file[$i])) . '</text>';
                    }
                    $errorMsg .= $code;
                }
            }
            $errorMsg .= '</pre><div class="title" style="display: flex;justify-content: flex-end"><div>Max&nbsp;&nbsp;<a href="https://github.com/topyao/max">Github</a>&nbsp;&nbsp<a href="https://packagist.org/packages/max/max">Packagist</a></div></div></div></body>';
        } else {
            $handle   = $this->app->config->get('app.exception_handler', Handler::class);
            $errorMsg = (new $handle($e))->__toString();
        }
        return $errorMsg;
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

<?php
declare(strict_types=1);

namespace Max\Console;

/**
 * 错误和异常注册类
 * Class Error
 * @package Max
 */
class Error extends \Max\Foundation\Error
{
    /**
     * 异常回调函数
     * @param \Exception $exception
     */
    public function exception($exception)
    {
        $file = $exception->getFile();
        $line = $exception->getLine();
        $message = $exception->getMessage();
        $code = $exception->getCode() ?? '200';
        $this->log->write('error', 'CLI: ' . $message, ['File: ' => $file, 'Line: ' => $line, 'Code: ' => $code]);
        exit($message . "\n");
    }
}

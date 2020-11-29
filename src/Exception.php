<?php

namespace yao;

use Throwable;

class Exception extends \Exception
{
    /**
     * 异常提示
     * @var string
     */
    private string $message;
    /**
     * 异常代码
     * @var string
     */
    private string $code;

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        set_error_handler([$this, 'error']);
        set_exception_handler([$this, 'exception']);

    }

    public function exception()
    {

    }

    public function error()
    {

    }
}
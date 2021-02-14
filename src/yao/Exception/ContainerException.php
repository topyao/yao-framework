<?php

namespace Yao\Exception;

use Throwable;

class ContainerException extends \RuntimeException implements \Psr\Container\ContainerExceptionInterface
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $this->message = __CLASS__ . ':' . $message;
    }

}
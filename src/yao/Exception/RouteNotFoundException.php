<?php


namespace Yao\Exception;


use Throwable;

class RouteNotFoundException extends \RuntimeException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $this->message = __CLASS__ . ':' . $message;
    }

}
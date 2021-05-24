<?php
declare(strict_types=1);

namespace Max\Exception;

use Throwable;

class ContainerException extends \RuntimeException implements \Psr\Container\ContainerExceptionInterface
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
//        $this->message = __CLASS__ . ':' . $message;
    }

}
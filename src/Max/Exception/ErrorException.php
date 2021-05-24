<?php
declare(strict_types=1);

namespace Max\Exception;

use Throwable;

class ErrorException extends \Exception
{

    public function __construct($code, string $message, string $file, int $line)
    {
        $this->message = $message;
        $this->file    = $file;
        $this->line    = $line;
        $this->code    = (int)$code;
    }

    public function getSeverity()
    {
        return $this->severity;
    }

}
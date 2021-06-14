<?php

namespace Max\Exception;

use Throwable;

class Handler
{

    /**
     * 异常实例
     * @var Throwable
     */
    protected $exception;

    /**
     * Handle constructor.
     * @param Throwable $e
     */
    public function __construct(Throwable $e)
    {
        $this->exception = $e;
    }

    public function __toString()
    {
        return <<<TOR
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$this->exception->getMessage()}</title>
    <style>
        html,
        body {
            background-color: #fff;
            color: #636b6f;
            font-family: "Nunito", sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .code {
            border-right: 2px solid;
            font-size: 26px;
            padding: 0 15px 0 15px;
            text-align: center;
        }

        .message {
            font-size: 18px;
            text-align: center;
        }
    </style>
</head>

<body>
<div class="flex-center position-ref full-height">
    <div class="code">
        {$this->exception->getCode()}
    </div>

    <div class="message" style="padding: 10px;">
        {$this->exception->getMessage()}
    </div>
</div>
</body>

</html>
TOR;
    }

}
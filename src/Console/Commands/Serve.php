<?php

namespace Yao\Console\Commands;

use Yao\Console\Command;

class Serve extends Command
{

    // public $command = '';

    // public $port = 8080;

    public function out()
    {
        echo '输入运行的端口[为空默认8080]:';
        fscanf(STDIN, '%d', $options);
        $port = $options ?? 8080;

        echo <<<EOT
********************************************************
*                         Yao                          *
*        RUNNING!  https://github.com/topyao/yao       *
********************************************************

EOT;

        passthru('php -S 127.0.0.1:' . $port . ' -t public ./public/router.php');
    }


    // public function p(...$args)
    // {
    //     $this->port = $args[0];
    // }
}

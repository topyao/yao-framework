<?php

namespace Yao\Console\Commands;

use Yao\Console\Command;

class Serve extends Command
{

    public $command = 'php -S 127.0.0.1';

    public $port = 8080;

    public function out()
    {
        echo <<<EOT
********************************************************
*                         Yao                          *
*        RUNNING!  https://github.com/topyao/yao       *
********************************************************

EOT;

        passthru($this->command . ':' . $this->port . ' -t public ./public/router.php');
    }


    public function p(...$args)
    {
        $this->port = $args[0];
    }

}
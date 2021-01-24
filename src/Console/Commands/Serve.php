<?php

namespace Yao\Console\Commands;

use Yao\Console\Command;

class Serve extends Command
{
    public function out()
    {
        echo '输入运行的端口[为空默认8080]:';
        fscanf(STDIN, '%d', $options);
        $port = $options ?? 8080;

        echo <<<EOT
+------------------------------------------------------+
|                         Yao                          |
|             https://github.com/topyao/yao            |
+------------------------------------------------------+
Welcome                        Press 'CTRL + C' to quit.

EOT;
        passthru('php -S localhost:' . $port . ' -t public ./public/router.php');
    }
}

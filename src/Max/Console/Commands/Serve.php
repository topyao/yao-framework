<?php
declare(strict_types=1);

namespace Max\Console\Commands;

use Max\Console\Command;

class Serve extends Command
{
    public function out()
    {
        echo '输入运行的端口[为空默认8080]:';
        fscanf(STDIN, '%d', $port);

        echo <<<EOT
+------------------------------------------------------+
|                         MaxPHP                       |
|             https://github.com/topyao/max            |
+------------------------------------------------------+
Welcome!                       Press 'CTRL + C' to exit.

EOT;
        passthru('php -S 127.0.0.1:' . ($port ?? 8080) . ' -t public ./server.php');
    }
}

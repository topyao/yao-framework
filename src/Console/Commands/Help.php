<?php

namespace Yao\Console\Commands;

use Yao\Console\Command;

class Help extends Command
{

    public function out()
    {
        echo <<<EOT
serve                    -运行程序
migrate <className>      -执行迁移文件
route                    -路由操作

EOT;
    }
}

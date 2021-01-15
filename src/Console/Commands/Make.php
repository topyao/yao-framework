<?php

namespace Yao\Console\Commands;

class Make
{
    public function out()
    {
        echo <<<EOT
        (1). 生成控制器
        (2). 生成模型
        (3). 退出

EOT;
        fscanf(STDIN, '%s', $options);
    }
}

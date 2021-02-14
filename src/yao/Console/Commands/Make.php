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
输入要生成的文件<1,2,3>：
EOT;
        fscanf(STDIN, '%d', $options);
    }
}

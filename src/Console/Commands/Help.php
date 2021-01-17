<?php

namespace Yao\Console\Commands;

use Yao\Console\Command;

class Help extends Command
{

    public function out()
    {
        echo <<<EOT
(1). 启动服务[php yao serve]
(2). 数据操作[暂时不支持]
(3). 路由操作[php yao route]
(4). 退出
输入选项<1,2,3>：
EOT;
        $command = [
            1 => 'php yao serve',
            2 => 'help',
            3 => 'php yao route',
        ];
        while (1) {
            fscanf(STDIN, '%d', $options);
            if (4 == $options) {
                exit;
            }
            if (array_key_exists($options, $command)) {
                passthru($command[$options]);
                exit;
            } else {
                echo "输入错误！重新输入<CTRL+C退出>：";
            }
        }
    }
}

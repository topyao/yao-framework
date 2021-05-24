<?php
declare(strict_types=1);

namespace Max\Console\Commands;

use Max\Console\Command;

class Help extends Command
{

    public function out()
    {
        echo <<<EOT
(1). 启动服务[php max serve]
(2). 新建资源
(3). 路由操作[php max route] 重大bug，暂时不要使用
(4). 退出
输入选项<1,2,3>：
EOT;
        $commandsMap = [
            1 => 'php max serve',
            2 => 'php max make',
            3 => 'php max route',
        ];
        fscanf(STDIN, '%d', $options);
        if (4 == $options) {
            exit;
        }
        if (array_key_exists($options, $commandsMap)) {
            passthru($commandsMap[$options]);
            exit;
        } else {
            echo "输入错误！重新输入<CTRL+C退出>：";
        }
    }
}

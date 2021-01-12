<?php

namespace Yao\Console\Commands;

class Help
{
    public function out()
    {
        return <<<EOT
        serve [-p 8080]          -以8080端口(默认)运行程序
        migrate <className>      -执行迁移文件
        route                    -查看定义的路由
             -cache              --生成给路由缓存
             -dcache             --删除路由缓存

EOT;
    }
}

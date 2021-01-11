<?php


namespace Yao\Console\Commands;


use Yao\Console\Command;

class Route extends Command
{
    const ROUTEFILE = ROOT . 'bootstrap' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'routes.php';

    public function out()
    {
        echo $this->out;
    }

    public function l()
    {
        \Yao\Facade\Route::register();
        return print_r(array_filter(\Yao\Facade\Route::getRoute()), true);
    }

    public function cache()
    {
        \Yao\Facade\Route::register();
        file_put_contents(self::ROUTEFILE, serialize(array_filter(\Yao\Facade\Route::getRoute())));
        return "缓存生成成功\n";
    }

    public function dcache()
    {
        if (!file_exists(self::ROUTEFILE)) {
            return "没有缓存文件！\n";
        }
        unlink(self::ROUTEFILE);
        return "缓存生成已经清除\n";
    }
}
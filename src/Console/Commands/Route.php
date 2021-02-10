<?php

namespace Yao\Console\Commands;

use Yao\Console\Command;

class Route extends Command
{

    public $cacheFile;
    const SEPARATOR = "+------+-------------------------+--------------------------------------------------+----------------+\n";

    public function __construct()
    {
        $this->cacheFile = env('storage_path') . 'cache' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'routes.php';
    }


    private function _format($string, $length)
    {
        return str_pad($string, $length, ' ', STR_PAD_BOTH);
    }

    public function out()
    {
        echo <<<EOT
(1). 输出路由列表
(2). 生成路由缓存
(3). 删除路由缓存
(4). 退出
请输入选项<1,2,3,4>：
EOT;
        while (1) {
            fscanf(STDIN, '%d', $options);
            if (!empty($options)) {
                switch ($options) {
                    case 1:
                        \Yao\Facade\Route::register();
                        echo self::SEPARATOR . "|" . $this->_format(' 请求', 6) . " |" . $this->_format('请求地址', 29) . "|" . $this->_format('路由地址', 54) . "|  " . $this->_format('别名', 16) . "|\n" . self::SEPARATOR;
                        foreach (\Yao\Facade\Route::getRoute() as $method => $routes) {
                            foreach ($routes as $route => $locate) {
                                if (is_array($locate['route'])) {
                                    $locate['route'] = implode('->', $locate['route']);
                                } else if ($locate['route'] instanceof \Closure) {
                                    $locate['route'] = '\Closure';
                                }
                                echo '|' . $this->_format(strtoupper($method), 6) . '|' . $this->_format($route, 25) . '|' . $this->_format($locate['route'], 50) . '| ' . $this->_format(\Yao\App::instance()->make('alias')->getAliasByUri($route), 15) . "|\n";
                            }
                        }
                        exit(self::SEPARATOR);
                    case 2:
                        if (!file_exists(dirname($this->cacheFile))) {
                            mkdir(dirname($this->cacheFile), 0777, true);
                        }
                        if (file_exists($this->cacheFile)) {
                            unlink($this->cacheFile);
                        }
                        \Yao\Facade\Route::register();
                        file_put_contents($this->cacheFile, serialize(array_filter(\Yao\Facade\Route::getRoute())));
                        exit("缓存生成成功\n");
                    case 3:
                        if (!file_exists($this->cacheFile)) {
                            exit("没有缓存文件！\n");
                        }
                        unlink($this->cacheFile);
                        exit("缓存生成已经清除\n");
                    case 4:
                        exit;
                    default:
                        echo "输入错误！\n重新输入选项[输入4退出]：";
                }
            }
        }
    }
}

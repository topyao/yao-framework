<?php
declare(strict_types=1);

namespace Max\Console\Commands;

use Max\Console\Command;
use Max\Http\Route\Alias;

class Route extends Command
{

    /**
     * 缓存文件
     * @var string
     */
    protected $cacheFile;

    const SEPARATOR = "+------+----------------------------------------------+--------------------------------------------------+----------------+\n";

    /**
     * 命令地图
     * @var string[]
     */
    protected $routeMap = [
        1 => 'list',
        2 => 'createCache',
        3 => 'deleteCache',
    ];

    public function __construct()
    {
        $this->cacheFile = env('storage_path') . 'cache' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'routes.php';
    }

    public function out()
    {
        echo <<<EOT
(1). 输出路由列表
(2). 生成路由缓存
(3). 删除路由缓存
请输入选项<1,2,3>：
EOT;
        fscanf(STDIN, '%d', $options);
        if (!isset($this->routeMap[$options])) {
            return $this->out();
        }
        return call_user_func([$this, $this->routeMap[$options]]);
    }

    /**
     * 路由列表输出
     */
    public function list()
    {
        \Max\Facade\Route::register();
        echo self::SEPARATOR . "|" . $this->_format(' 请求', 6) . " |" . $this->_format('请求地址', 50) . "|" . $this->_format('路由地址', 54) . "|  " . $this->_format('别名', 16) . "|\n" . self::SEPARATOR;
        foreach (\Max\Facade\Route::all() as $method => $routes) {
            foreach ($routes as $route => $locate) {
                $location = $locate['route'];
                if (is_array($location)) {
                    $location = implode('@', $location);
                } else if ($location instanceof \Closure || 'C:' === substr($location, 0, 2)) {
                    $location = '\Closure';
                }
                echo '|' . $this->_format(strtoupper($method), 6) . '|' . $this->_format($route, 46) . '|' . $this->_format($location, 50) . '| ' . $this->_format(app(Alias::class)->getAliasByUri($route), 15) . "|\n";
            }
        }
        exit(self::SEPARATOR);
    }

    /**
     * 生成路由缓存
     * 因为php串行化闭包问题，如果路由中存在闭包会报错
     */
    public function createCache()
    {
        if (!file_exists(dirname($this->cacheFile))) {
            mkdir(dirname($this->cacheFile), 0777, true);
        }
        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }
        \Max\Facade\Route::register();
        $routes = \Max\Facade\Route::all();
        foreach ($routes as $method => $route) {
            foreach ($route as $path => $location) {
                if ($location['route'] instanceof \Closure) {
                    $routes[$method][$path]['route'] = \Opis\Closure\serialize($location['route']);
                }
            }
        }
        file_put_contents($this->cacheFile, serialize(array_filter($routes)));
        exit("缓存生成成功\n");
    }

    /**
     * 删除路由缓存
     */
    public function deleteCache()
    {
        if (!file_exists($this->cacheFile)) {
            exit("没有缓存文件！\n");
        }
        unlink($this->cacheFile);
        exit("缓存已经清除\n");
    }


    /**
     * 格式化文本，给两端添加空格
     * @param $string
     * @param $length
     * @return string
     */
    private function _format($string, $length)
    {
        return str_pad($string, $length, ' ', STR_PAD_BOTH);
    }

}

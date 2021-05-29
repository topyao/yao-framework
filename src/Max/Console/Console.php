<?php
declare(strict_types=1);

namespace Max\Console;

use Max\Console\Commands\Config;
use Max\Console\Commands\Help;
use Max\Foundation\App;

class Console
{

    /**
     * @var App
     */
    protected $app;
    /**
     * 命令
     * @var mixed|string
     */
    public $command = '';

    /**
     * 参数
     * @var array
     */
    public $argv = [];

    /**
     * 框架预定义命令
     * @var array|string[]
     */
    protected $register = [];

    protected $builtIn = [
        'serve'  => Commands\Serve::class,
        'route'  => Commands\Route::class,
        'make'   => Commands\Make::class,
        'config' => Config::class
    ];

    public function __construct($argv, App $app)
    {
        $this->app = $app;
        $app['provider']->serve($this->app['config']->get('app.provider.cli', []));
        $this->app->make(Error::class)->register();
        if (!function_exists('passthru')) {
            exit('环境不支持passthru函数，请取消禁用！');
        }
        if (!isset($argv[1])) {
            exit((new Help())->out());
        }
        $commands = array_merge($this->register, $this->builtIn);
        if (isset($commands[$argv[1]])) {
            $this->command = $commands[$argv[1]];
        }
        $this->argv = array_slice($argv, 2);
    }

    /**
     * 运行
     */
    public function run()
    {
        if (!class_exists($this->command)) {
            return (new Help())->out();
        }
        return $this->app
            ->make($this->command, $this->argv, false)
            ->out();
    }
}

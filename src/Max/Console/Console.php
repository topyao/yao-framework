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

    public function __construct(App $app)
    {
        if (!function_exists('passthru')) {
            exit('环境不支持passthru函数，请取消禁用！');
        }
        $app->make(Error::class)->register();
        $this->app = $app;
    }

    /**
     * 动态添加命令
     * @param $command
     * @param $handle
     */
    public function add($command, $handle)
    {
        $this->register[$command] = $handle;
        return $this;
    }

    public function run()
    {
        global $argv;

        $this->app->provider->serve($this->app->config->get('app.provider.cli', []));

        $commands = array_merge($this->register, $this->builtIn);

        if (!isset($argv[1]) || !isset($commands[$argv[1]])) {
            exit((new Help())->out());
        }

        return $this->app
            ->make($commands[$argv[1]], array_slice($argv, 2))
            ->out();
    }
}

<?php

namespace Yao\Console;

use Yao\Console\Commands\Help;
use Yao\Facade\Config;

class Console
{

    /**
     * 命令
     * @var mixed|string
     */
    public string $command = '';

    /**
     * 参数
     * @var array
     */
    public array $argv = [];

    /**
     * 框架预定义命令
     * @var array|string[]
     */
    protected array $register = [
        'serve' => Commands\Serve::class,
        'route' => Commands\Route::class,
    ];

    public function __construct($argv)
    {
        if (!isset($argv[1])) {
            exit((new Help())->out());
        }
        $userCommands = Config::get('console');
        $commands = array_merge((array)$userCommands, $this->register);
        if (array_key_exists($argv[1], $commands)) {
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
        $command = new $this->command($this->argv);
        if (is_scalar($command->out())) {
            return $command->out();
        }
    }
}

<?php

namespace Yao\Console;

use Yao\Console\Commands\Help;

class Console
{

    public $command;

    public $argv;

    public function __construct($argv)
    {
        if (!isset($argv[1])) {
            exit((new Help())->out());
        }
        if (file_exists($commands = env('root_path') . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'commands.php')) {
            $userCommands = include_once($commands);
            is_array($userCommands) || $userCommands = [];
        }
        $builtInCommands = include_once(__DIR__ . DIRECTORY_SEPARATOR . 'register.php');
        $commands = array_merge($userCommands, $builtInCommands);
        if (array_key_exists($argv[1], $commands)) {
            $this->command = $commands[$argv[1]];
        }
        $this->argv = array_slice($argv, 2);
    }

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

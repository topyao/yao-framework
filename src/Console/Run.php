<?php

namespace Yao\Console;

use Yao\Console\Commands\Help;

class Run
{

    public $command;

    public $argv;

    public function __construct($argv)
    {
        $this->command = '\\Yao\\Console\\Commands\\' . ucfirst($argv[1]);
        $this->argv = array_slice($argv, 2);
    }

    public function out()
    {
        if (!class_exists($this->command)) {
            return (new Help())->out();
        }
        $command = new $this->command($this->argv);
        if (is_scalar($command->out())) {

        }
    }

}
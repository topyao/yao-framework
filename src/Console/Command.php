<?php


namespace Yao\Console;


use Yao\Console\Commands\Help;

abstract class Command
{
    // public $argv;
    // public $options = [];

    // public $out;

    // public function __construct($argv)
    // {
    //     $this->argv = $argv;
    //     $this->call();
    // }

    // public function make()
    // {
    //     if (!empty($this->argv)) {
    //         $this->pull(array_shift($this->argv));
    //         $this->make();
    //     }
    // }

    // public function pull($option)
    // {
    //     if ('-' == substr($option, 0, 1)) {
    //         if (!isset($this->argv[0]) || '-' == $this->argv[0]) {
    //             $this->options[substr($option, 1)] = [];
    //         } else {
    //             $this->options[substr($option, 1)] = [array_shift($this->argv)];
    //         }
    //     } else {
    //         $this->options[end($this->options)] .= '' . $option;
    //     }
    // }

    // public function call()
    // {
    //     $this->make();
    //     foreach ($this->options as $function => $option) {
    //         if (method_exists($this, $function)) {
    //             $this->out .= call_user_func_array([$this, $function], $option);
    //         } else {
    //             return (new Help())->out();
    //         }
    //     }
    // }

    abstract public function out();
}
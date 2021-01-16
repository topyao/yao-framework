<?php


namespace Yao\Console;


use Yao\Console\Commands\Help;

abstract class Command
{
    abstract public function out();
}
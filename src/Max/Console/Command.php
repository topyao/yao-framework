<?php
declare(strict_types=1);

namespace Max\Console;

abstract class Command
{
    abstract public function out();

    protected function writeLine(string $format, string ...$args)
    {
        echo sprintf($format, $args);
    }
}
<?php


namespace Yao\View;


abstract class Driver
{

    protected $config = [];

    public function __construct()
    {
        $this->config = config('view');
    }

    abstract public function render($template, $arguments = []);
}
<?php

namespace Yao\View;

use Yao\Facade\Config;

class Render
{

    public $driver;

    public function __construct()
    {
        Config::load('view');
        if (!class_exists($driver = Config::get('view.type'))) {
            $driver = 'Yao\\View\\Drivers\\' . ucfirst(Config::get('view.type'));
        }
        $this->driver = $driver;
    }


    public function render($template, $arguments = [])
    {
        return $this->driver::instance($template)
            ->render($arguments);
    }
}

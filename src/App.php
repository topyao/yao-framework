<?php

namespace Yao;

class App
{
    public static function run()
    {
        \Yao\Facade\Event::serve();
        \Yao\Facade\Route::dispatch();
    }
}
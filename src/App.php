<?php

namespace Yao;

class App
{
    public static function run()
    {
        \Yao\Facade\Provider::serve();
        \Yao\Facade\Route::dispatch();
    }
}
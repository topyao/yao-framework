<?php

namespace Yao;

use Yao\Http\Request;

class App
{
    public static function run()
    {
        \Yao\Facade\Provider::serve();

//        foreach (\Yao\Facade\Route::getMiddleware() as $middleware) {
//            $d = (new $middleware)->handle(\Yao\Facade\Request::instance(), function ($request) {
//
//            });
//        }

        \Yao\Facade\Route::dispatch();
    }

}
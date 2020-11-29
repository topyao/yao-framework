<?php


namespace yao;


class App
{
    public static function run()
    {
        \yao\facade\Service::serve();
//        (new \app\http\Service())->serve();
        \yao\facade\Route::dispatch();
    }
}
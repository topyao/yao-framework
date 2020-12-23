<?php

namespace Yao\Event;

class Init
{
    public function boot()
    {
        \Yao\Facade\Env::load();
        new \Yao\Exception();
        \Yao\Facade\Route::match();
        if (PHP_VERSION < 7.4) {
            throw new \Exception('PHP版本太低，建议升级到PHP7.4', 403);
        }
        //是否默认开启session
        if (\Yao\Facade\Config::get('app.auto_start')) {
            session_start();
            //session闪存检查
            \Yao\Facade\Session::flashCheck();
        }
        //设置默认时区
        date_default_timezone_set(\Yao\Facade\Config::get('app.default_timezone'));
    }
}
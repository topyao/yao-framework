<?php

namespace Yao\Services;

use Yao\Facade\Config;
use Yao\Interfaces\Service;

class Init implements Service
{
    public function register()
    {
    }

    public function boot()
    {
        ob_start();
        Config::load('app');
        if (PHP_VERSION < 7.4) {
            throw new \Exception('PHP版本太低，建议升级到PHP7.4', 110);
        }
        \Yao\Error::register();
        \Yao\Facade\Route::register();
        \Yao\Facade\Route::match();
        //是否默认开启session
        if (\Yao\Facade\Config::get('app.auto_start')) {
            session_start();
            //session闪存检查
            \Yao\Facade\Session::flashCheck();
        }
        //设置默认时区
        date_default_timezone_set(\Yao\Facade\Config::get('app.default_timezone', 'PRC'));

    }
}

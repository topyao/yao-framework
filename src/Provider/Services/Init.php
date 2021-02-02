<?php

namespace Yao\Provider\Services;

use Yao\Error;
use Yao\Facade\{Config, Route, Session};
use Yao\Provider\Service;

/**
 * 框架初始化服务
 * Class Init
 * @package Yao\Services
 */
class Init implements Service
{
    public function register()
    {
    }

    public function boot()
    {
        Route::register();
        Route::match();
        //是否默认开启session
        if (Config::get('app.auto_start')) {
            session_start();
            //session闪存检查
            Session::flashCheck();
        }
        //设置默认时区
        date_default_timezone_set(Config::get('app.default_timezone', 'PRC'));
    }
}

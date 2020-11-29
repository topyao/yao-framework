<?php

namespace yao\services;

class Init
{
    public function boot()
    {
        set_error_handler('error_handler');
        //接管异常
        set_exception_handler('exception_handler');
        if (PHP_VERSION < 7.4) {
            throw new \Exception('PHP版本太低，建议升级到PHP7.4', 403);
        }
        //装载env变量
        \yao\facade\Env::load();
        //关闭调试后显示屏蔽错误
        if (\yao\facade\Config::get('app.debug')) {
            ini_set('display_errors', 'On');
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 'Off');
            error_reporting(0);
        }
        //设置默认时区
        date_default_timezone_set(\yao\facade\Config::get('app.default_timezone'));
    }
}
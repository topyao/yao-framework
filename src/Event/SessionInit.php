<?php


namespace Yao\Event;


class SessionInit
{
    public function boot()
    {
        //是否默认开启session
        if (\Yao\Facade\Config::get('app.auto_start')) {
            session_start();
            //session闪存检查
            \Yao\Facade\Session::flashCheck();
        }
    }
}
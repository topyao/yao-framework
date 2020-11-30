<?php


namespace yao\event;


class SessionInit
{
    public function boot()
    {
        //是否默认开启session
        if (\yao\facade\Config::get('app.auto_start')) {
            session_start();
            //session闪存检查
            \yao\facade\Session::flashCheck();
        }
    }
}
<?php

namespace Yao\Provider\Services;

use Yao\Provider\Service;
use Yao\Route\Route;
use Yao\App;

/**
 * 框架初始化服务
 * Class Init
 * @package Yao\Services
 */
class Init implements Service
{
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function register()
    {
    }

    public function boot()
    {
        //是否默认开启session
        if ($this->app['config']->get('app.auto_start')) {
            session_start();
            //session闪存检查
            $this->app['session']->flashCheck();
        }
        //设置默认时区
        date_default_timezone_set($this->app['config']->get('app.default_timezone', 'PRC'));
    }
}

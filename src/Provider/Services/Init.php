<?php

namespace Yao\Provider\Services;

use Yao\App;
use Yao\Config;
use Yao\Http\Session;
use Yao\Provider\Service;

/**
 * 框架初始化服务
 * Class Init
 * @package Yao\Services
 */
class Init implements Service
{
    protected App $app;

    protected Config $config;

    protected Session $session;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->config = $app['config'];
        $this->session = $app['session'];
    }

    public function register()
    {
    }

    public function boot()
    {
        //是否默认开启session
        if ($this->config->get('app.auto_start')) {
            session_start();
            //session闪存检查
            $this->session->flashCheck();
        }
        //设置默认时区
        date_default_timezone_set($this->config->get('app.default_timezone', 'PRC'));
    }
}

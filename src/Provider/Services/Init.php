<?php

namespace Yao\Provider\Services;

use Yao\Provider\Service;
use Yao\Route\Route;

/**
 * 框架初始化服务
 * Class Init
 * @package Yao\Services
 */
class Init implements Service
{

    public function __construct(\Yao\Config $config, \Yao\Http\Session $session)
    {
        $this->config = $config;
        $this->session = $session;
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

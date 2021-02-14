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

    /**
     * App实例
     * @var App
     */
    protected App $app;

    /**
     * Config实例
     * @var mixed|object|Config
     */
    protected Config $config;

    /**
     * Session实例
     * @var mixed|object|Session
     */
    protected Session $session;

    /**
     * 初始化实例列表
     * Init constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->config = $app['config'];
        $this->session = $app['session'];
    }

    /**
     * 服务注册
     */
    public function register()
    {
    }

    /**
     * 服务启动
     */
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

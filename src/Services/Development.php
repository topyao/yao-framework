<?php


namespace Yao\Services;


use Yao\Interfaces\Service;

/**
 * 开发环境服务类
 * Class Development
 * @package Yao\Services
 */
class Development implements Service
{
    public function register()
    {

    }

    public function boot()
    {
        \Yao\Facade\Env::load();
    }
}
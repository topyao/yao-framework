<?php

namespace Yao\Provider;

/**
 * 服务接口
 * Interface Service
 * @package Yao\Provider
 */
interface Service
{
    public function boot();
    public function register();
}

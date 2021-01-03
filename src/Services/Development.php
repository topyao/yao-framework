<?php


namespace Yao\Services;


use Yao\Interfaces\Service;

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
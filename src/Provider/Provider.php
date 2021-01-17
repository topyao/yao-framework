<?php


namespace Yao\Provider;


use Yao\Provider\Service;
use Yao\Provider\Services\{
    Development,
    Init
};

/**
 * 服务提供类
 * Class Provider
 * @package Yao
 */
class Provider
{

    public function services()
    {
        return [];
    }

    public function builtInServices()
    {
        return [
            Init::class
        ];
    }

    public function serve()
    {
        $services = [...($this->services() ?: []), ...$this->builtInServices()];
        foreach ($services as $service) {
            if (in_array(Service::class, class_implements($service))) {
                call_user_func([new $service, 'boot']);
            } else {
                throw new \Exception("{$service}没有实现服务接口");
            }
        }
    }
}
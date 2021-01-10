<?php


namespace Yao;


use Yao\Interfaces\Service;

class Provider
{

    public function services()
    {
        return [];
    }

    public function builtInServices()
    {
        return [
            \Yao\Services\Init::class
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
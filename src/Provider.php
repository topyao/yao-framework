<?php


namespace Yao;


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
            call_user_func([new $service, 'boot']);
        }
    }
}
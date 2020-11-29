<?php


namespace yao;


class Service
{

    public function services()
    {
        return [];
    }

    public function builtInServices()
    {
        return [
            \yao\services\Init::class,
            \yao\services\SessionInit::class
        ];
    }

    public function serve()
    {
        $services = [...($this->builtInServices() ?: []), ...$this->services()];
        foreach ($services as $service) {
            call_user_func([new $service, 'boot']);
        }
    }
}
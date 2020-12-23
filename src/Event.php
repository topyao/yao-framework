<?php


namespace Yao;


class Event
{

    public function services()
    {
        return [];
    }

    public function builtInServices()
    {
        return [
            \Yao\Event\Init::class
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
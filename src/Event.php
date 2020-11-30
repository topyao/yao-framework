<?php


namespace yao;


class Event
{

    public function services()
    {
        return [];
    }

    public function builtInServices()
    {
        return [
            \yao\event\Init::class,
            \yao\event\SessionInit::class
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
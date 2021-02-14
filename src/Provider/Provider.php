<?php


namespace Yao\Provider;

use Yao\Database\DatabaseService;
use Yao\Provider\Services\Init;

/**
 * 服务提供类
 * Class Provider
 * @package Yao
 */
class Provider
{

    /**
     * 用户自定义服务列表
     * @return array
     */
    public function services()
    {
        return [];
    }

    /**
     * 内置服务列表
     * @return string[]
     */
    final public function builtInServices()
    {
        return [
            Init::class,
            DatabaseService::class
        ];
    }

    /**
     * 服务启动方法
     * @throws \Exception
     */
    public function serve()
    {
        $services = [...(array)$this->services(), ...$this->builtInServices()];
        foreach ($services as $service) {
            if (in_array(Service::class, class_implements($service))) {
//                \Yao\Container::instance()->invokeMethod([$service, 'boot']);
                call_user_func([new $service, 'boot']);
            } else {
                throw new \Exception("{$service}没有实现服务接口");
            }
        }
    }
}
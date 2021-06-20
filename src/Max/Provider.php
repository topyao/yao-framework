<?php
declare(strict_types=1);

namespace Max;

/**
 * 服务提供类
 * Class Provider
 * @package Max
 */
class Provider
{

    /***
     * 容器实例
     * @var App
     */
    protected $app;

    /**
     * Provider constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 服务提供者
     * @param array $services
     * @throws \Exception
     */
    public function serve(array $services)
    {
        //php7.4新语法[...$arr1, ...$arr2]
        foreach ($services as $service) {
            if (!class_exists($service)) {
                throw new \Exception("服务不存在: {$service}");
            }
            $service = $this->app->make($service, [], false);
            call_user_func([$service, 'register']);
            call_user_func([$service, 'boot']);
        }
    }

}

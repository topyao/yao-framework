<?php
declare(strict_types=1);

namespace Max;

/**
 * 基础类
 * class Service
 * @package Max
 */
abstract class Service implements \Max\Contracts\Service
{

    /**
     * App
     * @var App
     */
    protected $app;

    /**
     * Service constructor.
     * @param App $app
     */
    final public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 服务注册方法
     * @return mixed
     */
    abstract public function register();

    /**
     * 服务启动方法
     * @return mixed
     */
    abstract public function boot();

}

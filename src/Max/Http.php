<?php


namespace Max;

class Http
{

    /**
     * @var App
     */
    protected $app;

    /**
     * app配置
     * @var array
     */
    protected $config;

    public function __construct(App $app)
    {
        $this->config = $app->config->get('http');
        date_default_timezone_set($this->config['default_timezone']);
        $app->error->register();
        $app->serve($this->config['provider'] ?? []);
        $this->app = $app;
    }

    public function response()
    {
        return $this->app->middleware
            ->through($this->config['middleware'] ?? [])
            ->then(function () {
                return $this->app->route->register()->dispatch();
            })->end();
    }

    public function end($response)
    {
        ob_start();
        return $this->app->response
            ->body($response)
            ->send();
    }

}

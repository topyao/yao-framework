<?php


namespace Max;


use Max\Foundation\App;

class Http
{

    /**
     * @var App
     */
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function response()
    {
        ob_start();
        $config = $this->app['config']->get('app');
        $this->app['error']->register();
        $this->app['lang']->import($config['language']);
        $this->app['provider']->serve($config['provider']['http'] ?? []);
        date_default_timezone_set($config['default_timezone']);
        return $this->app->middleware
            ->through($config['middleware'])
            ->then(function () {
                return $this->app['route']->register()->dispatch();
            })->end();
    }

    public function end($response)
    {
        return $this->app->response
            ->body($response)
            ->send();
    }

}
<?php


namespace Max;

class Http
{

    /**
     * @var App
     */
    protected $app;

    /**
     * @var array
     */
    protected $config;

    public function __construct(App $app)
    {
        $this->config = $app->config->get('app');
        $app->error->register();
	$app->serve($this->config['provider'] ?? []);
        $this->app = $app;
    }

    public function response()
    {
        $this->app['lang']->import($this->config['language']);
        date_default_timezone_set($this->config['default_timezone']);
        return $this->app->middleware
            ->through($this->config['middleware'])
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

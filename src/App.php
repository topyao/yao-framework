<?php

namespace Yao;

defined('ROOT_PATH') || define('ROOT_PATH', dirname(getcwd()) . DIRECTORY_SEPARATOR);

/**
 * Class App
 * @package Yao
 */
class App extends Container
{
    /**
     * 绑定的类名
     * @var array|string[]
     */
    protected array $bind = [
        'request' => \Yao\Http\Request::class,
        'validate' => \App\Http\Validate::class,
        'file' => \Yao\File::class,
        'app' => \Yao\App::class,
        'env' => \Yao\Env::class,
        'config' => \Yao\Config::class,
        'view' => \Yao\View\Render::class,
        'route' => \Yao\Route\Route::class,
        'error' => \Yao\Error::class,
        'provider' => \Yao\Provider\Provider::class,
        'response' => \Yao\Http\Response::class,
        'session' => \Yao\Http\Session::class,
        'log' => \Yao\Log::class
    ];

    public function __construct()
    {
    }


    public function init()
    {

    }

    private function _setEnv()
    {
        $this->env->set('ROOT_PATH', ROOT_PATH);
        $this->env->set('APP_PATH', ROOT_PATH . 'app' . DIRECTORY_SEPARATOR);
        $this->env->set('YAO_PATH', __DIR__ . DIRECTORY_SEPARATOR);
        $this->env->set('CONFIG_PATH', ROOT_PATH . 'config' . DIRECTORY_SEPARATOR);
        $this->env->set('STORAGE_PATH', ROOT_PATH . 'storage' . DIRECTORY_SEPARATOR);
        $this->env->set('ROUTES_PATH', ROOT_PATH . 'routes' . DIRECTORY_SEPARATOR);
        $this->env->set('VIEWS_PATH', ROOT_PATH . 'views' . DIRECTORY_SEPARATOR);
        $this->env->set('PUBLIC_PATH', ROOT_PATH . 'public' . DIRECTORY_SEPARATOR);
        $this->env->set('CACHE_PATH', ROOT_PATH . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR);
    }

    public function run()
    {
        ob_start();
        $this['error']->register();
        $this['route']->register();
        $this->route->match();
        $this['provider']->serve();
        $this->route->dispatch();
    }

}

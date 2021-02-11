<?php

namespace Yao;

use App\Http\Validate;
use Yao\Http\{Middleware, Request, Response, Session};
use Yao\Provider\Provider;
use Yao\Route\{Route, Rules\Alias};
use Yao\View\Render;

defined('ROOT_PATH') || define('ROOT_PATH', dirname(getcwd()) . DIRECTORY_SEPARATOR);

/**
 * @property Request $request
 * @property Validate $validate
 * @property Env $env
 * @property Config $config
 * @property Render $view
 * @property Route $route
 * @property Response $response
 * @property Session $session
 * @property Log $log
 * @property Alias $alias
 * @property Provider $provider
 * @property Middleware $middleware
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
        'request' => Request::class,
        'validate' => Validate::class,
        'file' => File::class,
        'app' => App::class,
        'env' => Env::class,
        'config' => Config::class,
        'view' => Render::class,
        'route' => Route::class,
        'error' => Error::class,
        'provider' => Provider::class,
        'response' => Response::class,
        'session' => Session::class,
        'log' => Log::class,
        'alias' => Alias::class,
        'middleware' => Middleware::class
    ];

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

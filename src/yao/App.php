<?php
declare(strict_types=1);

namespace Yao;

use App\Http\Validate;
use Yao\Http\{Middleware, Request, Response, Route, Route\Alias, Session};
use Yao\Provider\Provider;
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

    /**
     * App初始化方法
     */
    protected function init()
    {

    }

    /**
     * 环境变量设置
     */
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

    /**
     * App运行
     * @throws \Exception
     */
    public function run()
    {
        set_time_limit(30);
        @ini_set('memory_limit', '64M');
//        ignore_user_abort(true);
        ob_start();
        if ($this['config']->get('app.auto_start')) {
            session_start();
            $this['session']->flashCheck();
        }
        date_default_timezone_set($this->config->get('app.default_timezone', 'PRC'));
        $this['error']->register();
        $this->bind = array_merge((array)$this->config->get('app.alias'), $this->bind);
        $this['route']->register();
        $this->route->match();
//        $this['provider']->serve();
        $this->route->dispatch();
    }

}

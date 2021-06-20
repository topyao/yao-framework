<?php
declare (strict_types=1);

namespace Max;

use App\Console\Console;
use Max\Http\{Middleware, Request, Response, Route, Session};
use Max\Lang\Lang;

/**
 * @property Request $request    请求实例
 * @property Env $env            环境变量实例
 * @property Config $config      配置类实例
 * @property Response $response  响应实例
 * @property Session $session    Session实例
 * @property Log $log            日志类实例
 * @property Route $route        路由实例
 * @property Lang $lang          多语言
 * Class App
 * @author chengyao
 * @version 1.0.0
 * @package Max
 */
class App extends Container
{

    /**
     * 绑定的类名
     * @var array|string[]
     */
    protected $bind = [
        'http'       => Http::class,
        'request'    => Request::class,
        'env'        => Env::class,
        'config'     => Config::class,
        'route'      => Route::class,
        'error'      => Error::class,
        'response'   => Response::class,
        'session'    => Session::class,
        'log'        => Log::class,
        'middleware' => Middleware::class,
        'provider'   => Provider::class,
        'lang'       => Lang::class,
        'console'    => Console::class
    ];

    public function __construct()
    {
        $class                     = static::class;
        $this->bind['app']         = $class;
        static::$instances[$class] = $this;
        $this->bind                = array_merge($this->config->get('app.alias'), $this->bind);
    }

    public function rootPath()
    {
        return ('cli' === PHP_SAPI ? getcwd() : dirname($_SERVER['DOCUMENT_ROOT'])) . '/';
    }

}

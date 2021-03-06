<?php
declare (strict_types=1);

namespace Max;

use Max\Http\{Middleware, Request, Response, Route, Session};

/**
 * @property Request $request    请求实例
 * @property Env $env            环境变量实例
 * @property Config $config      配置类实例
 * @property Response $response  响应实例
 * @property Session $session    Session实例
 * @property Log $log            日志类实例
 * @property Route $route        路由实例
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
    ];

    public function __construct()
    {
        $class                     = static::class;
        $this->bind['app']         = $class;
        static::$instances[$class] = $this;
        $config                    = $this->config->get('app');
        $this->bind                = array_merge($config['alias'], $this->bind);
        date_default_timezone_set($config['default_timezone']);
    }

    /**
     * 服务提供者
     * @param array $services
     * @throws \Exception
     */
    public function serve(array $services)
    {
        foreach ($services as $service) {
            if (!class_exists($service)) {
                throw new \Exception("服务不存在: {$service}");
            }
            $service = $this->make($service, [], true);
            call_user_func([$service, 'register']);
            call_user_func([$service, 'boot']);
        }
    }

    public function rootPath()
    {
        return ('cli' === PHP_SAPI ? getcwd() : dirname($_SERVER['DOCUMENT_ROOT'])) . '/';
    }

}

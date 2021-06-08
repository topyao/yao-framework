<?php
declare (strict_types=1);

namespace Max\Foundation;

use App\Console\Console;
use Max\Event\Event;
use Max\Http;
use Max\Http\Middleware;
use Max\Http\Request;
use Max\Http\Response;
use Max\Http\Route;
use Max\Http\Route\Alias;
use Max\Http\Session;
use Max\Lang\Lang;

/**
 * @property Request $request    请求实例
 * @property Env $env        环境变量实例
 * @property Config $config     配置类实例
 * @property Response $response   响应实例
 * @property Session $session    Session实例
 * @property Log $log        日志类实例
 * @property Route $route      路由实例
 * @property Event $event      事件实例
 * @property Middleware $middleware 中间件实例
 * @property Lang $lang  多语言
 * @property Console $console
 * Class App
 * @package Max
 */
class App extends Container
{
    /**
     * 绑定的类名
     * php7.4可以使用protected array $bind;
     * @var array|string[]
     */
    protected $bind = [
        'http'       => Http::class,
        'request'    => Request::class,
        'file'       => \Max\FileSystem\File::class,
        'app'        => App::class,
        'env'        => Env::class,
        'config'     => Config::class,
        'route'      => Route::class,
        'error'      => Error::class,
        'response'   => Response::class,
        'session'    => Session::class,
        'log'        => \Max\Log\Log::class,
        'event'      => Event::class,
        'alias'      => Alias::class,
        'middleware' => Middleware::class,
        'provider'   => Provider::class,
        'lang'       => Lang::class,
        'console'    => Console::class
    ];

    public function __construct()
    {
        self::$instances[__CLASS__] = $this;
    }

    public function rootPath()
    {
        return ('cli' === PHP_SAPI ? getcwd() : dirname($_SERVER['DOCUMENT_ROOT'])) . '/';
    }

}

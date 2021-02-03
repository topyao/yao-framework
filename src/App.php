<?php

namespace Yao;

use Yao\Http\Request;
use Yao\Provider\Provider;
use Yao\Route\Route;

defined('ROOT_PATH') || define('ROOT_PATH', dirname(getcwd()) . DIRECTORY_SEPARATOR);

/**
 * Class App
 * @package Yao
 */
class App extends Container
{

    private $request;

    public function __construct(Request $request, Error $error, Route $route, Provider $provider)
    {
        $this->provider = $provider;
        $this->request = $request;
        $this->error = $error;
        $this->route = $route;
    }


    public function run()
    {
        ob_start();
        if (PHP_VERSION < 7.4) {
            throw new \Exception('PHP版本太低，建议升级到PHP7.4', 110);
        }
        $this->error->register();
        $this->provider->serve();
        $this->route->dispatch();
    }

}

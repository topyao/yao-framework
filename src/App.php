<?php

namespace Yao;

use Yao\Http\Request;

defined('ROOT_PATH') || define('ROOT_PATH', dirname(getcwd()) . DIRECTORY_SEPARATOR);

/**
 * Class App
 * @package Yao
 */
class App extends Container
{

    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    public function run()
    {
        ob_start();
        if (PHP_VERSION < 7.4) {
            throw new \Exception('PHP版本太低，建议升级到PHP7.4', 110);
        }
        Error::register();
        \Yao\Facade\Provider::serve();
        \Yao\Facade\Route::dispatch();
    }

}

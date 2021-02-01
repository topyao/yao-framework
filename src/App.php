<?php

namespace Yao;

defined('ROOT_PATH') || define('ROOT_PATH', dirname(getcwd()) . DIRECTORY_SEPARATOR);

/**
 * Class App
 * @package Yao
 */
class App
{

    private $request;

    public function __construct(\Yao\Http\Request $request)
    {
        $this->request = $request;
    }


    public function run()
    {
        \Yao\Facade\Provider::serve();
        \Yao\Facade\Route::dispatch();
    }

}

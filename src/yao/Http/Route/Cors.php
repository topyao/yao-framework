<?php


namespace Yao\Http\Route;

use Yao\Facade\Config;
use Yao\Facade\Request;
use Yao\Route\Route;
use Yao\Route\Rule;
use Yao\Traits\SingleInstance;

/**
 * 跨域支持类
 * Class Cors
 * @package Yao\Route
 */
class Cors
{
    use SingleInstance;

    protected array $cors = [];

    private function __construct()
    {
        $this->cors = Config::get('cors');
    }

    public function allowCors()
    {
        if (isset($this->routes['options'][Request::path()])) {
            header('Access-Control-Allow-Methods', 'get');
            header('Access-Control-Allow-Origin:' . $this->routes['options'][Request::path()]['originUrl']);
            header('Access-Control-Allow-Credentials:true');
            header('Access-Control-Allow-Headers:Origin,Content-Type,Accept,token,X-Requested-With');
        }
    }


    public function cross($AllowOrigin = null, $AllowCredentials = null, $AllowHeaders = null): Route
    {
        if (is_array($this->method)) {
            foreach ($this->method as $method) {
                $this->routes[$method][$this->path]['cors'] = [
                    'origin' => $AllowOrigin,
                    'credential' => $AllowCredentials,
                    'header' => $AllowHeaders
                ];
            }
        } else {
            $this->routes[$this->method][$this->path]['cors'] = [
                'origin' => $AllowOrigin,
                'credential' => $AllowCredentials,
                'header' => $AllowHeaders
            ];
        }
        return $this;
    }

}
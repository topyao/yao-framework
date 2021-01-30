<?php

namespace Yao\Http;

use Yao\Facade\Route;
use Yao\Traits\SingleInstance;

/**
 * 响应类
 * Class Response
 * @package Yao\Http
 */
class Response
{

    protected $response;
    protected $code = 200;
    protected $header = ['Content-Type:text/html; charset=UTF-8', 'X-Powered-By:YaoPHP'];
    protected $data;


    public function data($data)
    {
        $this->data = $data;
        return $this;
    }

    public function code($code = null)
    {
        isset($code) && $this->code = $code;
        return $this;
    }

    protected function _corsCheck()
    {

    }

    public function header($header = null)
    {
        $this->header = [...$this->header, ...(array)$header];
        return $this;
    }

    protected function create()
    {
        \Yao\Facade\Route::allowCors();
        http_response_code($this->code);
        foreach ($this->header as $header) {
            header($header);
        }
    }

    public function return()
    {
        $this->create();
        ob_end_flush();
        echo $this->data;
    }

}

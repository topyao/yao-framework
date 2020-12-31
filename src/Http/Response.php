<?php

namespace Yao\Http;

class Response
{

    protected $response;
    protected $code = 200;
    protected $header;
    protected $data;


    public function data($data)
    {
        $this->data = $data;
        return $this;
    }

    public function code($code)
    {
        $this->code = $code;
        return $this;
    }

    public function header($header)
    {
        $this->header = $header;
        return $this;
    }

    protected function create($data = null, $code = null, $header = null)
    {
//        $data ??= $this->data;
//        $code ??= $this->code;
//        $header ??= $this->header;
        \Yao\Facade\Route::allowCors();
        http_response_code($this->code);
        if (is_array($this->header)) {
            foreach ($this->header as $header) {
                header($header);
            }
        } else if (is_string($this->header)) {
            header($header);
        }
    }

//    public function return($request)
//    {
//        if (is_string($request)) {
//            return $this->create();
//        }
//    }

    public function __destruct()
    {
        $this->create();
        ob_end_flush();
        exit($this->data);
    }
}

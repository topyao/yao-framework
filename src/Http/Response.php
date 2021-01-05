<?php

namespace Yao\Http;

class Response
{

    protected $response;
    protected $code = 200;
    protected $header = ['Content-Type:text/html'];
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
        if (is_string($header)) {
            $this->header[] = $header;
        } else {
            $this->header += $header;
        }
        return $this;
    }

    protected function create()
    {
        \Yao\Facade\Route::allowCors();
        http_response_code($this->code);
        if (is_array($this->header)) {
            foreach ($this->header as $header) {
                header($header);
            }
        } else if (is_string($this->header)) {
            header($this->header);
        }
    }

    public function __destruct()
    {
        $this->create();
        ob_end_flush();
        exit($this->data);
    }
}

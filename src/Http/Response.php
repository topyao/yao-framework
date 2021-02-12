<?php

namespace Yao\Http;

use Yao\App;
use Yao\Facade\Config;

/**
 * 响应类
 * Class Response
 * @package Yao\Http
 */
class Response
{

    protected App $app;
    protected $response;
    protected $code = 200;
    protected $header = ['Content-Type:text/html; charset=UTF-8', 'X-Powered-By:YaoPHP'];
    protected $data;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function data($data)
    {
        if ($data instanceof \Closure) {
            $data = $data();
        }
        if (is_array($data)) {
            $this->header('Content-Type:application/json; charset=UTF-8');
            $data = json_encode($data, 256);
        }
        $this->data = $data;
        return $this;
    }

    public function code($code = null)
    {
        isset($code) && $this->code = $code;
        return $this;
    }

    public function cors($allows)
    {
        $origin = $allows['origin'] ?? $this->app->configget('cors.origin');
        $credentials = $allows['credentials'] ?? ($this->app->configget('cors.credentials') ? 'true' : 'false');
        $headers = $allows['headers'] ?? $this->app->configget('cors.headers');
        $this->header([
            'Access-Control-Allow-Origin:' . $origin,
            'Access-Control-Allow-Credentials:' . $credentials,
            'Access-Control-Allow-Headers:' . $headers
        ]);
        return $this;
    }

    public function header($header = null)
    {
        $this->header = [...$this->header, ...(array)$header];
        return $this;
    }

    protected function create()
    {
        $this->app->route->allowCors();
        foreach ($this->header as $header) {
            header($header);
        }
        http_response_code($this->code);
    }

    public function return()
    {
        $this->create();
        ob_end_flush();
        echo $this->data;
    }

}

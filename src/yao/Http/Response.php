<?php

namespace Yao\Http;

use Yao\App;

/**
 * 响应类
 * Class Response
 * @package Yao\Http
 */
class Response
{

    /**
     * 容器实例
     * @var App
     */
    protected App $app;

    /**
     * 响应状态码
     * @var int
     */
    protected int $code = 200;

    /**
     * 响应头信息
     * @var array|string[]
     */
    protected array $header = ['Content-Type:text/html; charset=UTF-8', 'X-Powered-By:YaoPHP'];

    /**
     * 响应的额外数据
     * @var
     */
    protected $data;

    /**
     * 初始化容器实例
     * Response constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 添加响应数据
     * @param \Closure|array|string $data
     * @return $this
     */
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

    /**
     * 设置响应状态码
     * @param null|int $code
     * @return $this
     */
    public function code(?int $code = null)
    {
        isset($code) && $this->code = $code;
        return $this;
    }

    /**
     * 跨域支持，该方法目前不可用
     * @param $allows
     * @return $this
     */
    public function cors($allows)
    {
        $origin = $allows['origin'] ?? $this->app->config->get('cors.origin');
        $credentials = $allows['credentials'] ?? ($this->app->config->get('cors.credentials') ? 'true' : 'false');
        $headers = $allows['headers'] ?? $this->app->config->get('cors.headers');
        $this->header([
            'Access-Control-Allow-Origin:' . $origin,
            'Access-Control-Allow-Credentials:' . $credentials,
            'Access-Control-Allow-Headers:' . $headers
        ]);
        return $this;
    }

    /**
     * 设置响应头
     * @param null|string|array $header
     * @return $this
     */
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

    /**
     * 响应执行
     */
    public function return()
    {
        $this->create();
        ob_end_flush();
        echo $this->data;
    }

}

<?php
declare(strict_types=1);

namespace Max\Http\Route;

use Max\App;
use Max\Http\Request;
use Max\Http\Response;

/**
 * 跨域支持类
 * Class Cors
 * @package Max\Route
 */
class Cors
{

    /**
     * @var App
     */
    protected $app;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Cors constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app      = $app;
        $this->request  = $app['request'];
        $this->response = $app['response'];
    }

    /**
     * 允许跨域
     */
    public function allow()
    {
        if ($this->response->hasHeader('Access-Control-Allow-Origin')) {
            if ($this->request->isMethod('options')) {
                return $this->response->withStatus(204)->send();
            }
        }
    }

    /**
     * AllowOrigin
     * @param $origin
     * @return $this
     */
    public function setAllowOrigin($origin)
    {
        if ('*' == $origin) {
            $this->response->withHeader('Access-Control-Allow-Origin', '*');
        } else if (in_array($allowOrigin = $this->request->header('origin'), (array)$origin)) {
            $this->response->withHeader('Access-Control-Allow-Origin', $allowOrigin);
        }
        return $this;
    }

    /**
     * AllowHeaders
     * @param $allowHeaders
     * @return $this
     */
    public function setAllowHeaders($allowHeaders)
    {
        $this->response->withHeader('Access-Control-Allow-Headers', $allowHeaders);
        return $this;
    }

    /**
     * AllowCredentials
     * @param $allowCredentials
     * @return $this
     */
    public function setCredentials($allowCredentials)
    {
        $this->response->withHeader('Access-Control-Allow-Credentials', $allowCredentials);
        return $this;
    }

    /**
     * AllowMethod
     * @param string $method
     * @return $this
     */
    public function setAllowMethod(string $method)
    {
        $this->response->withHeader('Access-Control-Allow-Methods', strtoupper($method));
        return $this;
    }

    /**
     * MaxAge
     * @param int $maxAge
     * @return $this
     */
    public function setMaxAge(int $maxAge)
    {
        $this->response->withHeader('Access-Control-Max-Age:', $maxAge);
        return $this;
    }

}

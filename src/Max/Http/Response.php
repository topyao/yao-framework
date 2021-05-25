<?php
declare(strict_types=1);

namespace Max\Http;

use Max\Foundation\App;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * 响应类
 * Class Response
 * @package Max\Http
 */
class Response extends HttpMessage implements ResponseInterface
{

    protected $header = [];

    protected $code = 200;

    protected $body;

    /**
     * 容器实例
     * @var App
     */
    protected $app;

    const CHARSET = 'UTF-8';

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
     * 响应体
     * @param $body
     * @return $this
     * @throws \Exception
     */
    public function body($body)
    {
        if ($body instanceof static) {
            return $this;
        }

        if ($body instanceof \Closure) {
            return $this->body($body());
        }

        if (is_array($body)) {
            $this->contentType('application/json');
            $body = json($body);
        } else {
            $this->contentType('text/html');
        }

        if (!is_scalar($body) && !is_null($body)) {
            throw new \Exception('Invalid type of response body: ' . gettype($body));
        }
        echo $body;
//        $this->body = $body;
        return $this;
    }

    /**
     * 响应的contentType头
     * @param string $contentType
     * @param string|null $charset
     * @return $this
     */
    public function contentType(string $contentType, string $charset = null)
    {
        $this->withHeader('Content-Type', $contentType . '; charset=' . ($charset ?? static::CHARSET));
        return $this;
    }

    public function cache(int $expire)
    {
        $this->withHeader('Cache-Control', 'max-age=' . $expire);
        return $this;
    }

    public function redirect(string $url, int $code = 302)
    {
        $this->withHeader('location', $url)
            ->withStatus($code)
            ->send();
    }

    public function getProtocolVersion()
    {
        // TODO: Implement getProtocolVersion() method.
    }

    public function withProtocolVersion($version)
    {
        // TODO: Implement withProtocolVersion() method.
    }

    public function withHeader($name, $value)
    {
        $this->header[$name] = $value;
        return $this;
    }

    public function withAddedHeader($name, $value)
    {
        // TODO: Implement withAddedHeader() method.
    }

    public function withoutHeader($name)
    {
        if (isset($this->header[$name])) {
            unset($this->header[$name]);
        }
        return $this;
    }

    public function getBody()
    {
        if ($this->body instanceof static) {
            return $this;
        }

        if ($this->body instanceof \Closure) {
            $this->body = ($this->body)();
            return $this->getBody();
        }

        if (is_array($this->body)) {
            $this->contentType('application/json');
            $this->body = json($this->body);
        }

        if (!is_scalar($this->body) && !is_null($this->body)) {
            throw new \Exception('Invalid type of response body: ' . gettype($body));
        }

        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        $this->body = $body;
        return $this;
    }

    public function getStatusCode()
    {
        return $this->code;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $this->code = $code;
        return $this;
    }

    public function getReasonPhrase()
    {
        // TODO: Implement getReasonPhrase() method.
    }

    public function send()
    {
        http_response_code((int)$this->code);
        $this->withHeader('X-Powered-By', $this->app->config->get('app.powered_by', 'MaxPHP'));
        foreach ($this->header as $name => $value) {
            header("{$name}: {$value}");
        }
//        echo $this->getBody();
        ob_end_flush();
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

}

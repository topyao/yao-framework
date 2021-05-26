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

    /**
     * 响应头
     * @var array
     */
    protected $header = [];

    /**
     * 状态码
     * @var int
     */
    protected $code = 200;

    /**
     * 响应体
     * @var
     */
    protected $body;

    /**
     * 容器实例
     * @var App
     */
    protected $app;

    /**
     * 默认编码
     */
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
            return $this->json($body);
        } else {
            $this->contentType('text/html');
        }

        if (!is_scalar($body) && !is_null($body)) {
            throw new \Exception('Invalid type of response body: ' . gettype($body));
        }
        echo $body;
        return $this;
    }

    /**
     * Json数据响应
     * @param array $jsonSerializable
     * @return $this
     * @throws \Exception
     */
    public function json(array $jsonSerializable)
    {
        $this->contentType('application/json');
        echo json($jsonSerializable);
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

    /**
     * 响应缓存
     * @param int $expire
     * @return $this
     */
    public function cache(int $expire)
    {
        $this->withHeader('Cache-Control', 'max-age=' . $expire)
            ->withoutHeader('Pragma');
        return $this;
    }

    /**
     * 重定向
     * @param string $url
     * @param int $code
     */
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

    /**
     * 添加响应头
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function withHeader($name, $value)
    {
        $this->header[$name] = $value;
        return $this;
    }

    public function withAddedHeader($name, $value)
    {
        foreach (headers_list() as $header) {
            [$name, $value] = explode(': ', $header);
            $this->header[$name] = $value;
        }
        return $this;
    }

    /**
     * 移除响应头
     * @param string $name
     * @return $this
     */
    public function withoutHeader($name)
    {
        if (isset($this->header[$name])) {
            unset($this->header[$name]);
        }
        header_remove($name);
        return $this;
    }

    /**
     * 取得响应体
     * @return $this|bool|float|int|string
     * @throws \Exception
     */
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

    /**
     * Http状态码取得
     * @return int
     */
    public function getStatusCode()
    {
        return $this->code;
    }

    /**
     * 设置状态码
     * @param int $code
     * @param string $reasonPhrase
     * @return $this
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $this->code = $code;
        return $this;
    }

    public function getReasonPhrase()
    {
        // TODO: Implement getReasonPhrase() method.
    }

    /**
     * 发送响应
     */
    public function send()
    {
        http_response_code((int)$this->code);
        $this->withHeader('X-Powered-By', $this->app->config->get('app.powered_by', 'MaxPHP'));
        foreach ($this->header as $name => $value) {
            header("{$name}: {$value}");
        }
        ob_end_flush();
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

}

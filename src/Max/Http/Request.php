<?php
declare(strict_types=1);

namespace Max\Http;

use Max\Tools\Arr;
use Max\Foundation\{App, Config};
use Psr\Http\Message\{RequestInterface, StreamInterface, UriInterface};

/**
 * 请求类
 * Class Request
 * @package Max
 */
class Request implements RequestInterface
{

    /**
     * App类对象，用来获取容器内的实例
     * php7.4可以使用protected App $app;
     * @var App
     */
    protected $app;

    /**
     * Config类实例
     * @var mixed|object|Config
     */
    protected $config;

    /**
     * 请求参数过滤函数
     * @var mixed|string|null
     */
    protected $filters = [];

    /**
     * 当前请求的控制器
     * @var string
     */
    protected $controller;

    /**
     * 当前请求的方法
     * @var string
     */
    protected $action = '';

    /**
     * 路由参数
     * @var
     */
    protected $routeParams;

    /**
     * $_SERVER
     * @var array
     */
    protected $server = [];

    /**
     * 请求头信息
     * @var array
     */
    protected $header = [];

    /**
     * $_SESSION
     * @var array
     */
    protected $session = [];

    /**
     * $_COOKIE
     * @var array
     */
    protected $cookie = [];

    /**
     * $_ENV
     * @var array
     */
    protected $env = [];

    /**
     * $_FILES
     * @var array
     */
    protected $file = [];

    /**
     * $_GET
     * @var array
     */
    protected $get = [];
    /**
     * $_POST
     * @var array
     */
    protected $post = [];

    /**
     * PUT的参数
     * @var
     */
    protected $put;

    /**
     * $_REQUEST
     * @var array
     */
    protected $request = [];

    /**
     * 当前请求的类型
     * @var string
     */
    protected $method = '';

    /**
     * 当前请求的路径
     * @var string
     */
    protected $path = '/';

    /**
     * 当前请求的原始数据
     * @var false|string
     */
    protected $input = '';

    /**
     * Request constructor.
     * @param App $app
     */
    public function __construct()
    {
        $this->input = file_get_contents('php://input');
    }

    /**
     * 容器的setter方法
     * @param App $app
     * @return static
     */
    public static function __setter(App $app)
    {
        $request          = new static();
        $request->app     = $app;
        $request->env     = &$_ENV;
        $request->server  = &$_SERVER;
        $request->filters = $app->config->get('app.filter', []);

        $header = apache_request_headers();

        if (isset($request->server['CONTENT_TYPE'])) {
            $header['CONTENT-TYPE'] = $request->server['CONTENT_TYPE'];
        }
        if (isset($request->server['CONTENT_LENGTH'])) {
            $header['CONTENT-LENGTH'] = $request->server['CONTENT_LENGTH'];
        }

        $request->header  = array_change_key_case($header, CASE_UPPER);
        $request->method  = strtolower($request->server['REQUEST_METHOD'] ?? 'cli');
        $request->get     = &$_GET;
        $request->post    = &$_POST;
        $request->request = &$_REQUEST;
        $request->file    = &$_FILES;
        $request->cookie  = &$_COOKIE;
        $request->session = $_SESSION ?? [];
        $request->put     = $request->input;

        if (($path = parse_url($request->server['REQUEST_URI'] ?? '', PHP_URL_PATH)) && '/' !== $path) {
            $request->path = rtrim($path, '/');
        }

        return $request;

    }

    /**
     * 当前请求的控制器，设置和获取
     * @param null $controller
     * @return string|null
     */
    public function controller($controller = null)
    {
        if (!isset($controller)) {
            return $this->controller;
        }
        $this->controller = $controller;
    }

    /**
     * 当前请求的方法，设置和获取,高版本可以在类型前添加？表示可以为null
     * @param ?string|null $action
     * @return string
     */
    public function action($action = null)
    {
        if (!isset($action)) {
            return $this->action;
        }
        $this->action = $action;
    }

    /**
     * 路由参数
     * @param null $params
     * @return mixed
     */
    public function routeParams($params = null)
    {
        if (!isset($params)) {
            return $this->routeParams;
        }
        $this->routeParams = $params;
    }

    /**
     * 获取$_SERVER变量
     * @param string|null $name
     * 标识
     * @return array|mixed|null
     */
    public function server(string $name = null)
    {
        return $name ? ($this->server[strtoupper($name)] ?? null) : $this->server;
    }

    /**
     * 获取单个请求头
     * @param string $header
     * @return mixed|null
     */
    public function header(string $header)
    {
        return $this->header[strtoupper($header)] ?? null;
    }

    /**
     * 获取所有请求头
     * @return array
     */
    public function headers(): array
    {
        return $this->header;
    }

    /** 请求类型判断
     * @param string $method
     * 请求类型
     * @return bool
     */
    public function isMethod(string $method): bool
    {
        return $this->server('REQUEST_METHOD') === strtoupper($method);
    }

    /**
     * 获取请求的url
     * @param bool|null $full
     * @return string
     */
    public function url(bool $full = false): string
    {
        return ($this->server['REQUEST_SCHEME'] ?? 'http') . '://' . $this->server['HTTP_HOST'] . ($full ? $this->server['REQUEST_URI'] : '/');
    }

    /**
     * 获取请求uri
     * @return mixed
     */
    public function uri()
    {
        return $this->server['REQUEST_URI'];
    }

    /**
     * 获取请求查询字符串
     * @return mixed
     */
    public function queryString()
    {
        return $this->server['QUERY_STRING'];
    }

    /**
     * 获取请求的PATH
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * 可以获取客户端真实ip
     * @return bool|mixed|string
     */
    public function ip()
    {
        $ip = false;
        // 客户端IP 或 NONE
        if (isset($this->server['HTTP_CLIENT_IP'])) {
            $ip = $this->server['HTTP_CLIENT_IP'];
        }
        // 多重代理服务器下的客户端真实IP地址（可能伪造）,如果没有使用代理，此字段为空
        if (isset($this->server['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(', ', $this->server['HTTP_X_FORWARDED_FOR']);
            if ($ip) {
                array_unshift($ips, $ip);
                $ip = false;
            }
            for ($i = 0; $i < count($ips); $i++) {
                if (!preg_match('^(10│172.16│192.168).', $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        // 客户端IP 或 (最后一个)代理服务器 IP
        return ($ip ? $ip : $this->server['REMOTE_ADDR']);
    }

    /**
     * 当前请求的session
     * @return array
     */
    public function session(): array
    {
        return $this->session;
    }

    /**
     * 返回当前请求类型
     * @return string
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * cookie获取方法
     * @param null $field
     * @return array|mixed|null
     */
    public function cookie($field = null)
    {
        if (isset($field)) {
            if (is_string($field)) {
                return $this->cookie[strtoupper($field)] ?? null;
            }
            if (is_array($field)) {
                $return = [];
                foreach ($field as $key) {
                    $return[$key] = $this->cookie($key);
                }
                return $return;
            }
        }
        return $this->cookie;
    }

    /**
     * 判断是否ajax请求
     * @return bool
     */
    public function isAjax(): bool
    {
        return !empty($this->server['HTTP_X_REQUESTED_WITH']) && $this->server['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }

    /**
     * 获取查询参数
     * @param null $key
     * @return array|mixed|string|null
     * @throws \Exception
     */
    public function query($key = null)
    {
        if (is_null($key)) {
            return $this->server('QUERY_STRING');
        }
        if (is_string($key)) {
            parse_str($this->server('QUERY_STRING'), $query);
            if (isset($query[$key])) {
                return http_build_query([$key => $query[$key]]);
            }
            return null;
        }
        if (is_array($key)) {
            throw new \Exception('暂时不支持数组');
        }
    }

    /**
     * 判断请求的地址是否匹配当前请求的地址
     * @param string $path
     * @return bool
     */
    public function is(string $path): bool
    {
        return $this->path === $path || preg_match("#^{$path}$#iU", $this->path);
    }

    /**
     * get请求参数
     * @param string|array $key
     * 请求的参数列表
     * @param string|array $default
     * 字符串参数的默认值
     * @return array|string
     */
    public function get($key = null, $default = null)
    {
        return $this->request($this->get, $key, $default);
    }

    /**
     * 获取post参数
     * @param string|array $key 请求的参数列表
     * @param string|int $default 字符串参数的默认值
     * @return array|string
     */
    public function post($key = null, $default = null)
    {
        return $this->request($this->post, $key, $default);
    }

    /**
     * 获取提交的原始数据
     * @return false|string
     */
    public function input()
    {
        return $this->input;
    }

    /**
     * 获取请求中所有参数
     * @param string|array $key 请求的参数列表
     * @param string $default 字符串参数的默认值
     * @return array|string
     */
    public function param($key = null, $default = null)
    {
        return $this->request($this->request, $key, $default);
    }

    /**
     * 获取put的参数列表
     * @param null $field
     * @param null $default
     * @return array|string|null
     */
    public function put($field = null, $default = null)
    {
        parse_str($this->input(), $_PUT);
        return $this->request($_PUT, $field, $default);
    }

    /**
     * @param null $field
     * @param null $default
     * @return array|mixed|null
     */
    public function patch($field = null, $default = null)
    {
        parse_str($this->input(), $_PATCH);
        return $this->request($_PATCH, $field, $default);
    }

    /**
     * @param null $field
     * @param null $default
     * @return array|mixed|null
     */
    public function delete($field = null, $default = null)
    {
        parse_str($this->input(), $_DELETE);
        return $this->request($_DELETE, $field, $default);
    }

    /**
     * debug $_FILES 获取方法
     * @param null $field
     * @return array|mixed
     * @throws \Exception
     */
    public function file($field = null)
    {
        if (is_null($field)) {
            return $this->file;
        }
        if (isset($this->file[$field])) {
            return $this->file[$field];
        }
        throw new \Exception('No file upload: ' . $field, 404);
    }

    /**
     * 过滤方法
     * @param $params
     * @param null $key
     * @param null $default
     * @return array|mixed|null
     * @throws \Exception
     */
    protected function request($params, $key = null, $default = null)
    {
        if (!isset($key)) {
            return $this->filter($params);
        }
        if (is_scalar($key)) {
            return isset($params[$key])
                ? $this->filter($params[$key])
                : $default;
        }
        if (is_array($key)) {
            $return = $default = [];
            if (Arr::isAssoc($key)) {
                $key = Arr::getAssoc($key);
                [$default, $key] = [array_values($key), array_keys($key)];
            }
//            TODO 数字索引的时候有bug,这里仍然有bug，当为空字符串的时候
            foreach ($key as $k => $value) {
                if (isset($params[$value]) && !empty($params[$value])) {
                    $return[$value] = $this->filter($params[$value]);
                } else {
                    if (isset($default[$k])) {
                        $return[$value] = $default[$k];
                    }
                }
            }
            return $return;
        }
        return null;
    }

    /**
     * 使用过滤函数过滤值
     * @param $var
     * @return mixed
     * @throws \Exception
     */
    protected function filterVar(&$var, $key = null)
    {
        foreach ($this->filters as $filter) {
            if (false === function_exists($filter)) {
                throw new \Exception("过滤函数不存在：{$filter}", 503);
            }
            $var = $filter($var);
        }
        return $var;
    }

    /**
     * 针对字符串或者数组进行不同的过滤
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    protected function filter($params)
    {
        if (is_scalar($params)) {
            return $this->filterVar($params);
        }
        if (is_array($params) && array_walk_recursive($params, [$this, 'filterVar'])) {
            return $params;
        }
        throw new \Exception('参数过滤失败！');
    }

    /**
     * __get
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->$key;
    }

    /**
     * __set
     * @param $key
     * @param $value
     * @throws \Exception
     */
    public function __set($key, $value)
    {
        if (property_exists($this, $key)) {
            throw new \Exception("属性禁止被写入：{$key}");
        }
        $this->$key = $value;
    }


    /**
     * 取得所有Header
     * @return array
     */
    public function getHeaders()
    {
        return $this->header;
    }

    /**
     * 判断Header是否存在
     * @param string $name
     * @return bool
     */
    public function hasHeader($name)
    {
        return isset($this->header[strtoupper($name)]);
    }

    /**
     * 取得某一个Header
     * @param string $name
     * @return array
     */
    public function getHeader($name)
    {
        $name = strtoupper($name);
        if (isset($this->header[$name])) {
            $header = [$name, $this->header[$name]];
        }
        return $header ?? [];
    }

    /**
     * 取一行Header
     * @param string $name
     * @return string
     */
    public function getHeaderLine($name)
    {
        $name = strtoupper($name);
        if (isset($this->header[$name])) {
            $header = "{$name}: {$this->header[$name]}";
        }
        return $header ?? '';
    }


    public function getProtocolVersion()
    {
        return $this->server['SERVER_PROTOCOL'];
    }

    public function withProtocolVersion($version)
    {
    }


    public function withHeader($name, $value)
    {
        // TODO: Implement withHeader() method.
    }

    public function withAddedHeader($name, $value)
    {
        // TODO: Implement withAddedHeader() method.
    }

    public function withoutHeader($name)
    {
        // TODO: Implement withoutHeader() method.
    }

    public function getBody()
    {
        // TODO: Implement getBody() method.
    }

    public function withBody(StreamInterface $body)
    {
        // TODO: Implement withBody() method.
    }

    public function getRequestTarget()
    {
        // TODO: Implement getRequestTarget() method.
    }

    public function withRequestTarget($requestTarget)
    {
        // TODO: Implement withRequestTarget() method.
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function withMethod($method)
    {
        // TODO: Implement withMethod() method.
    }

    public function getUri()
    {
        return $this->server['REQUEST_URI'];
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        // TODO: Implement withUri() method.
    }
}

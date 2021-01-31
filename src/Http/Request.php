<?php

namespace Yao\Http;

/**
 * 请求类
 * Class Request
 * @package Yao
 */
class Request
{
    /**
     * 请求类型
     * @var mixed|string|null
     */
    protected ?array $filters = [];
    protected array $server = [];

    /**
     * 初始化请求类型
     * Request constructor.
     */
    public function __construct(?array $filters = null)
    {
        $this->server = $_SERVER;
        $this->filters = $filters ?? \Yao\Facade\Config::get('app.filter');
    }

    public function server(?string $name = null)
    {
        return $name ? ($this->server[strtoupper($name)] ?? null) : $this->server;
    }

    public function header(?string $header = null)
    {
        if (is_null($header)) {
            $headers = [];
            array_walk($this->server, function ($value, $key) use (&$headers) {
                if ('HTTP_' == substr($key, 0, 5)) {
                    $headers[$key] = $value;
                }
            });
            return $headers;
        }
        return $this->server('HTTP_' . strtoupper($header)) ?? null;
    }

    /** 请求类型判断
     * @param string $method
     * @return bool
     */
    public function isMethod(string $method): bool
    {
        return $this->server['REQUEST_METHOD'] == strtoupper($method);
    }

    public function url(): string
    {
        return $this->server['REQUEST_SCHEME'] . '://' . $this->server['HTTP_HOST'] . '/';
    }

    public function path()
    {
        //解析url中的path
        $path = parse_url($this->server['REQUEST_URI'], PHP_URL_PATH);
        //解析出错抛出异常终止脚本
        if (!$path) {
            throw new \Exception('页面不存在', 404);
        }
        //去掉右边斜线
        return ('/' == $path) ? $path : rtrim($path, '/');
    }

    public function method(): string
    {
        return strtolower($this->server['REQUEST_METHOD']);
    }

    public function cookie($field = null)
    {
        if (isset($field)) {
            if (is_string($field)) {
                return isset($_COOKIE[$field]) ? $_COOKIE[$field] : null;
            } else if (is_array($field)) {
                static $return = [];
                foreach ($field as $key) {
                    $return[$key] = $this->cookie($key);
                }
                return $return;
            }
        }
        return $_COOKIE;
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
     * get请求参数
     * @param string|array $key 请求的参数列表
     * @param string $default 字符串参数的默认值
     * @return array|string
     */
    public function get($key = null, $default = null)
    {
        return $this->_request($_GET, $key, $default);
    }

    /**
     * 获取post参数
     * @param string|array $key 请求的参数列表
     * @param string $default 字符串参数的默认值
     * @return array|string
     */
    public function post($key = null, $default = null)
    {
        return $this->_request($_POST ?: $this->_raw(), $key, $default);
    }

    /**
     * 获取请求中所有参数
     * @param string|array $key 请求的参数列表
     * @param string $default 字符串参数的默认值
     * @return array|string
     */
    public function param($key = null, $default = null)
    {
        return $this->_request($_REQUEST, $key, $default);
    }

    /**
     * 获取put的参数列表
     * @param null $field
     * @param null $default
     * @return array|string|null
     */
    public function put($field = null, $default = null)
    {
        parse_str(file_get_contents('php://input'), $put);
        return $this->_request($put, $field, $default);
    }

    public function file($field = null)
    {
        if (is_null($field)) {
            return $_FILES;
        }
        if (isset($_FILES[$field])) {
            return $_FILES[$field];
        } else {
            throw new \Exception('文件不存在', 404);
        }
    }

    private function _makeStringArgument($predefinedConstant, $argument, $default = '')
    {
        return isset($predefinedConstant[$argument])
            ? $this->_filter($predefinedConstant[$argument])
            : $default;
    }

    private function _makeArrayArguments($predefinedConstant, array $argument, $default = [])
    {
        $return = [];
        foreach ($argument as $key => $value) {
            $return[$value] = $predefinedConstant[$value] ?? ($default[$key] ?? null);
        }
        return $return;
    }

    private function _request($params, $key = null, $default = null)
    {
        if (!isset($key)) {
            return array_map(function ($value) {
                return $this->_filter($value);
            }, $params);
        }
        if (is_string($key)) {
            return $this->_makeStringArgument($params, $key, $default);
        }
        if (is_array($key)) {
            return $this->_makeArrayArguments($params, $key, $default);
        }
        return null;
    }

    private function _filter($params)
    {
        array_filter((array)$this->filters, function ($filter) use (&$params) {
            if (function_exists($filter)) {
                if (is_array($params)) {
                    foreach ($params as $key => $value) {
                        $params[$key] = $this->_filter($value);
                    }
                } else {
                    $params = $filter($params);
                }
            } else {
                throw new \Exception('过滤函数不存在！', 403);
            }
        });
        return $params;
    }
}

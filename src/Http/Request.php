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
    protected array $filters = [];
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

    public function server($name = null)
    {
        return $name ? ($this->server[strtoupper($name)] ?? null) : $this->server;
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
        return ($this->server['REQUEST_SCHEME'] ?? 'http') . '://' . $this->server['HTTP_HOST'] . '/';
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
        $put = $this->_raw();
        //        parse_str(file_get_contents('php://input'), $put);
        return $this->_request($put, $field, $default);
    }

    private function _raw()
    {
        parse_str(file_get_contents('php://input'), $output);
        return $output;
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


    public function instance()
    {
        return $this;
    }

    /**
     * 请求参数过滤方法
     * @param $params
     * @param null $key
     * @return array|string
     */
    private function _request($params, $key = null, $default = null)
    {
        //初始化参数列表
        $param = [];
        //当请求参数为空时直接返回空，空字符串和空数组均返回空
        if (!empty($params)) {
            //当没有设置$key参数时候返回所有参数
            if (!isset($key)) {
                //使用filter对参数的值进行过滤
                foreach ($params as $k => $v) {
                    $param[$k] = $this->_filter($v);
                }
            }
            //过滤参数为字符串
            if (is_string($key)) {
                //当传入的键存在于数组中时返回该值否则返回空
                $param = array_key_exists($key, $params) ? $this->_filter($params[$key]) : $default;
            }
            //过滤参数为数组
            if (is_array($key)) {
                foreach ($key as $field) {
                    if (array_key_exists($field, $params)) {
                        $param[$field] = $this->_filter($params[$field]);
                    } else {
                        $param[$field] = null;
                    }
                }
            }
        }
        //返回过滤后的数组
        return $param;
    }

    /**
     * 参数过滤方法
     * debug
     * @param $params
     * @return mixed
     */
    private function _filter($params)
    {
        !empty($this->filters) && array_filter($this->filters, function ($filter) use (&$params) {
            if (function_exists($filter)) {
                if (is_array($params)) {
                    foreach ($params as $key => $value) {
                        $params[$key] = $this->_filter($value);
                    }
                } else {
                    $params = $filter($params);
                }
            } else {
                throw new \Exception('过滤函数不存在！', 404);
            }
        });
        return $params;
    }
}

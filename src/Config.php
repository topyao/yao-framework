<?php

namespace Yao;

use Yao\Traits\Parse;

/**
 * 配置文件加载和访问类
 * Class Config
 * @package Yao
 */
class Config implements \ArrayAccess
{
    use Parse;

    public function offsetUnset($offset)
    {
    }

    public function offsetExists($offset)
    {
    }

    public function offsetGet($offset)
    {
        return $this->parse($this->config, $offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->config[$offset] = $value;
    }


    /**
     * 存放配置的数组
     * @var array
     */
    private array $config = [];

//    public function __construct()
//    {
//        array_map(function ($config) {
//            $config_suffix = substr($config, strrpos($config, DIRECTORY_SEPARATOR) + 1, -4);
//            $this->config[$config_suffix] = require_once($config);
//        }, glob($this->_getConfig('*')));
//    }


    /**
     * 配置文件获取方法
     * @param string $key
     * 使用点语法获取配置文件名下的配置文件，例如app.auto_start
     * @return array|mixed
     */
    public function get(?string $key = null, $default = null)
    {
        if (!isset($key)) {
            return $this->config;
        }
        return $this->parse($this->config, $key, $default);
    }

    public function getByType($config)
    {
        $config = $this->config[$config];
        return $config[$config['type']];
    }

    public function load($config)
    {
        if (!isset($this->config[$config])) {
            $file = $this->_getConfig($config);
            if (!file_exists($file)) {
                throw new \Exception('配置文件' . $config . '.php不存在');
            }
            $this->config[$config] = include_once($file);
        }
    }


    /**
     * 获取配置文件路径
     * @param string $config
     * @return string
     */
    private function _getConfig(string $config): string
    {
        return env('config_path') . $config . '.php';
    }
}

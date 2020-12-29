<?php

namespace Yao;

class Config
{
    use \Yao\Traits\Parse;

    /**
     * 存放配置的数组
     * @var array
     */
    private array $config = [];

    public function __construct()
    {
        array_map(function ($config) {
            $config_suffix = substr($config, strrpos($config, DIRECTORY_SEPARATOR) + 1, -4);
            $this->config[$config_suffix] = require_once($config);
        }, glob($this->_getConfig('*')));
    }


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
        return $this->getMultidimensionalArrayValue($this->config, $key, $default);
    }

    /**
     * 获取配置文件路径
     * @param string $config
     * @return string
     */
    private function _getConfig(string $config): string
    {
        return ROOT . 'config' . DIRECTORY_SEPARATOR . $config . '.php';
    }
}

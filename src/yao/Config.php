<?php
declare(strict_types=1);

namespace Yao;

use Yao\Traits\Parse;

/**
 * 配置文件加载和获取
 * Class Config
 * @package Yao
 */
class Config
{
    use Parse;

    /**
     * 存放配置的数组
     * @var array
     */
    protected array $config = [];


    public function __construct()
    {
        array_map(function ($config) {
            $config_suffix = substr($config, strrpos($config, DIRECTORY_SEPARATOR) + 1, -4);
            $this->config[$config_suffix] = include_once $config;
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
//        $this->load($key);
        if (!isset($key)) {
            return $this->config;
        }
        return $this->parse($this->config, $key, $default);
    }


    /*public function load($key)
    {
        if (is_null($key)) {
            array_map(function ($config) {
                $config_suffix = substr($config, strrpos($config, DIRECTORY_SEPARATOR) + 1, -4);
                $this->config[$config_suffix] = include_once($config);
            }, glob($this->_getConfig('*')));
        } else if (false !== ($point = strpos($key, '.'))) {
            $config = substr($key, 0, $point);
            if (!isset($this->config[$config])) {
                $this->config[$config] = include_once($this->_getConfig($config));
            }
        }
    }*/

    /**
     * 获取config配置中type对应的配置
     * @param $config
     * 只需要传入配置文件名，例如database
     * @return mixed
     */
    public function getByType($config)
    {
        $config = $this->config[$config];
        return $config[$config['type']];
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

<?php

namespace yao;

class Config
{

    /**
     * 存放配置的数组
     * @var array
     */
    private static ?array $config = [];

    /**
     * 配置文件获取方法
     * @param string $key
     * 使用点语法获取配置文件名下的配置文件，例如app.auto_start
     * @return array|mixed
     */
    public function get(?string $key = null)
    {
        $this->_load($key);
        if (!isset($key)) {
            return self::$config;
        }
        return getMultidimensionalArrayValue(self::$config, $key);


        // if (0 == substr_count($key, '.')) {
        //     if (!isset(self::$config[$key])) {
        //         throw new \Exception('配置参数' . $key . '不存在', 404);
        //     }
        //     return self::$config[$key];
        // }
        // $key_array = explode('.', $key);
        // if (!isset(self::$config[$key_array[0]][$key_array[1]])) {
        //     throw new \Exception('配置参数' . $key . '不存在', 404);
        // }
        // return self::$config[$key_array[0]][$key_array[1]];
    }


    /**
     * 配置文件加载
     * @param string|null $key
     * @throws \Exception
     */
    private function _load(?string $key = null): void
    {
        if (!isset($key)) {
            foreach (glob($this->_getConfig('*')) as $config) {
                $config_suffix = substr($config, strrpos($config, DS) + 1, -4);
                if (!isset(self::$config[$config_suffix])) {
                    self::$config[$config_suffix] = require_once($config);
                }
            }
        } else {
            $offset = strpos($key, '.');
            $key = $offset ? substr($key, 0, $offset) : $key;
            if (empty($key) || !file_exists($this->_getConfig($key))) {
                throw new \Exception("配置文件{$this->_getConfig($key)}不存在");
            }
            if (!isset(self::$config[$key])) {
                self::$config[$key] = include_once $this->_getConfig($key);
            }
        }
    }

    /**
     * 获取配置文件路径
     * @param string $config
     * @return string
     */
    private function _getConfig(string $config): string
    {
        return ROOT . 'config' . DS . $config . '.php';
    }
}

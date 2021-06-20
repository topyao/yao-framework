<?php
declare(strict_types=1);

namespace Max;

use Max\Tools\Str;

/**
 * 配置文件加载和获取
 * Class Config
 * @package Max
 */
class Config
{
    /**
     * 存放配置的数组
     * @var array
     */
    protected $config = [];

    public function __construct(App $app)
    {
        foreach (glob($app['env']->get('config_path') . '*.php') as $config) {
            $this->load($config);
        }
    }

    /**
     * 配置获取方法
     * @param string|null $key
     * 使用点语法获取配置文件名下的配置文件，例如app.auto_start
     * @param null $default
     * @return array|mixed|null
     */
    public function get(string $key = null, $default = null)
    {
        if (!isset($key)) {
            return $this->config;
        }
        return Str::parse($this->config, $key, $default);
    }

    /**
     * 加载配置
     * @param string $config
     */
    public function load(string $config)
    {
        $key                = substr($config, strrpos($config, '/') + 1, -4);
        $this->config[$key] = include $config;
    }

    /**
     * 获取config配置中default对应的配置
     * @param $config
     * 只需要传入配置文件名，例如database
     * @return mixed
     */
    public function getDefault($config)
    {
        $config = $this->config[$config];
        return $config[$config['default']];
    }

}

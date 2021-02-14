<?php
declare(strict_types=1);

namespace Yao;

/**
 * Env加载和获取
 * Class Env
 * @package Yao
 */
class Env
{

    use \Yao\Traits\Parse;

    /**
     * 存放env的数组
     * @var array
     */
    protected array $env = [];

    /**
     * root目录
     * @var string
     */
    protected string $root = '';

    public function __construct()
    {
        $this->root = ROOT_PATH;
        $this->_load();
    }

    private function _load(?string $envFile = null)
    {
        $this->env = [
            'ROOT_PATH' => $this->root,
            'APP_PATH' => $this->root . 'app' . DIRECTORY_SEPARATOR,
            'YAO_PATH' => dirname(__DIR__) . DIRECTORY_SEPARATOR,
            'CONFIG_PATH' => $this->root . 'config' . DIRECTORY_SEPARATOR,
            'STORAGE_PATH' => $this->root . 'storage' . DIRECTORY_SEPARATOR,
            'ROUTES_PATH' => $this->root . 'routes' . DIRECTORY_SEPARATOR,
            'VIEWS_PATH' => $this->root . 'views' . DIRECTORY_SEPARATOR,
            'PUBLIC_PATH' => $this->root . 'public' . DIRECTORY_SEPARATOR,
            'CACHE_PATH' => $this->root . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
        ];
        $env = $envFile ?? ($this->env['ROOT_PATH'] . '.env');
        if (file_exists($env)) {
            $env = parse_ini_file($env, true, INI_SCANNER_TYPED);
            $this->env += array_change_key_case($env, CASE_UPPER);
        }
    }

    /**
     * 设置env
     * @param string $env
     * @param null $value
     */
    public function set(string $env, $value = null)
    {
        $this->env[strtoupper($env)] = $value;
    }

    /**
     * 获取env
     * @param string|null $key
     * 标识
     * @param null $default
     * 默认值
     * @return array|mixed|null
     */
    public function get(?string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->env;
        }
        return $this->parse($this->env, strtoupper($key), $default);
    }
}

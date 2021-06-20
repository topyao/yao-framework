<?php
declare (strict_types = 1);

namespace Max;

use Max\Tools\Str;

/**
 * Env加载和获取
 * Class Env
 * @package Max
 */
class Env
{

    public function __construct(string $envFile = null)
    {
        //TODO 这里可能需要判断不同系统这个常量的值
        $root = app()->rootPath();
        $this->set('ROOT_PATH', $root)
            ->set('APP_PATH', $root . 'app/')
            ->set('CONFIG_PATH', $root . 'config/')
            ->set('STORAGE_PATH', $root . 'storage/')
            ->set('ROUTE_PATH', $root . 'routes/')
            ->set('PUBLIC_PATH', $root . 'public/')
            ->set('CACHE_PATH', $root . 'storage/cache/');
        if (file_exists($env = $envFile ?? ($root . '.env'))) {
            $this->add(parse_ini_file($env, true, INI_SCANNER_TYPED));
        }
    }

    /**
     * 设置env
     * @param string $env
     * @param null $value
     */
    public function set(string $env, $value)
    {
        $_ENV[strtoupper($env)] = $value;
        return $this;
    }

    public function add($env, $value = null)
    {
        if (is_null($value) && is_array($env)) {
            $_ENV = array_merge($_ENV, array_change_key_case($env, CASE_UPPER));
        } else {
            $this->set($env, $value);
        }
    }

    /**
     * 获取env
     * @param string|null $key
     * 标识
     * @param null $default
     * 默认值
     * @return array|mixed|null
     */
    public function get(string $key, $default = null)
    {
        return Str::parse($_ENV, strtoupper($key), $default);
    }

    public function all()
    {
        return $_ENV;
    }

    public function has($key)
    {
        return isset($_ENV[$key]);
    }
}

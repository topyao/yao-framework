<?php

namespace Yao;

/**
 * Env变量加载和获取类
 * Class Env
 * @package Yao
 */
class Env
{
    use \Yao\Traits\Parse;

    protected array $env = [];

    public function load(string $envFile = ROOT . '.env')
    {
        if (file_exists($envFile)) {
            $env = parse_ini_file($envFile, true, INI_SCANNER_TYPED);
            $this->env = array_change_key_case($env, CASE_UPPER);
        }
    }

    public function get(?string $key = null, $default = false)
    {
        if (is_null($key)) {
            return $this->env;
        }
        return $this->parse($this->env, strtoupper($key), $default);
    }
}

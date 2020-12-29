<?php

namespace Yao;

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

    public function get(string $key, $default = false)
    {
        $key = strtoupper($key);
        return $this->getMultidimensionalArrayValue($this->env, $key, $default);
    }
}

<?php

namespace Yao;

class Env
{

    protected array $env = [];

    public function load(string $envFile = ROOT . '.env')
    {
        if (file_exists($envFile)) {
            $env = parse_ini_file($envFile, true);
            $this->env = array_change_key_case($env, CASE_UPPER);
        }
    }

    public function get(string $key, $default = false)
    {
        $key = strtoupper($key);
        return getMultidimensionalArrayValue($this->env, $key, $default);


        // if (1 === count($key)) {
        //     $env = $this->env[strtoupper($key[0])] ?? $default;
        // } else if (2 == count($key)) {
        //     $env = $this->env[strtoupper($key[0])][strtoupper($key[1])] ?? $default;
        // }
        // return $env;
    }
}

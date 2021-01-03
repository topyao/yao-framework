<?php

namespace Yao\Route;

class Alias
{
    protected array $alias = [];

    public function set($alias, string $path)
    {
        if ($alias) {
            if (isset($this->alias[$alias])) {
                throw new \Exception("Path:'{$path}'的别名'{$alias}'已经被注册！");
            }
            $this->alias[$alias] = $path;
        }
    }

    public function get(?string $alias = null)
    {
        if (is_null($alias)) {
            return $this->alias;
        }
        return $this->alias[$alias] ?? $alias;
    }
}

<?php

namespace Yao\Route;


class Alias
{

    use \Yao\Traits\SingleInstance;

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

    public function get(?string $alias = null, array $args = [])
    {
        if (is_null($alias)) {
            return $this->alias;
        } else {
            if (isset($this->alias[$alias])) {
                if (preg_match('/\(.+\)/i', $this->alias[$alias])) {
                    $rep = explode(',', preg_replace(['#\\\#', '#\(.+\)#Ui'], ['', ','], $this->alias[$alias]));
                    $match = '';
                    foreach ($rep as $k => $r) {
                        $match .= ($r . ($args[$k] ?? ''));
                    }
                    return $match;
                } else {
                    return $this->alias[$alias];
                }
            }
        }
        return $this->alias[$alias] ?? $alias;
    }
}

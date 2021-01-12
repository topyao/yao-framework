<?php

namespace Yao\Route\Rules;


use Exception;
use Yao\Traits\SingleInstance;

/**
 * 路由别名类
 * Class Alias
 * @package Yao\Route
 */
class Alias
{
    use SingleInstance;

    protected array $alias = [];

    public function set($alias, string $path)
    {
        if ($alias) {
            if (isset($this->alias[$alias])) {
                throw new Exception("Path:'{$path}'的别名'{$alias}'已经被注册！");
            }
            $this->alias[$alias] = $path;
        }
    }

    public function get(string $alias, array $args = [])
    {
        if (isset($this->alias[$alias])) {
            if (preg_match('/\(.+\)/i', $this->alias[$alias])) {
                $rep = explode(',', preg_replace(['#\\\#', '#\(.+\)#Ui'], ['', ','], $this->alias[$alias]));
                if (($argNums = count($rep) - 1) != count($args)) {
                    throw new Exception("别名:{$alias}需要传入{$argNums}个参数！");
                }
                $match = '';
                $args = array_values($args);
                foreach ($rep as $k => $r) {
                    $match .= ($r . ($args[$k] ?? ''));
                }
                return $match;
            }
            return $this->alias[$alias];
        }
        return $alias;
    }
}

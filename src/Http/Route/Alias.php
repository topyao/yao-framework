<?php

namespace Yao\Http\Route;

use Exception;

/**
 * 路由别名类
 * Class Alias
 * @package Yao\Route
 */
class Alias
{

    /**
     * 存放注册的路由别名
     * @var array
     */
    protected array $alias = [];

    /**
     * 使用url获取路由别名
     * @param $url
     * @return false|int|string
     */
    public function getAliasByUri($url)
    {
        if (false !== ($key = array_search($url, $this->alias))) {
            return $key;
        }
        return '';
    }

    /**
     * 设置路由别名
     * @param string $alias
     * 路由别名
     * @param string $path
     * 路由地址
     * @throws Exception
     */
    public function set(string $alias, string $path)
    {
        if ($alias) {
            if (isset($this->alias[$alias])) {
                throw new Exception("Path:'{$path}'的别名'{$alias}'已经被注册！");
            }
            $this->alias[$alias] = $path;
        }
    }

    /**
     * 获取路由别名
     * @param string $alias
     * @param array $args
     * @return mixed|string
     * @throws Exception
     */
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

<?php


namespace Yao\Traits;

/**
 * 点语法解析Trait
 * Trait Parse
 * @package Yao\Traits
 */
trait Parse
{

    /**
     * 点语法解析
     * @param array $value
     * 数据源
     * @param string $string
     * 点语法字符串
     * @param mixed|null $default
     * 默认值
     * @return array|mixed|string|null
     */
    public function parse(array $value, string $string, $default = null)
    {
        $field = explode('.', $string);
        foreach ($field as $v) {
            if (isset($value[$v])) {
                $value = $value[$v];
            } else {
                $value = $default;
                break;
            }
        }
        return $value;
    }

}
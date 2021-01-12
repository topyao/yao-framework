<?php


namespace Yao\Traits;

/**
 * 点语法解析Trait
 * Trait Parse
 * @package Yao\Traits
 */
trait Parse
{
    public function parse($value, $string, $default = null)
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
<?php


namespace Yao\Traits;


trait Parse
{
    public function getMultidimensionalArrayValue($value, $string, $default = null)
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
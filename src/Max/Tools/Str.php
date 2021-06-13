<?php
declare(strict_types=1);

namespace Max\Tools;

class Str
{
    public static function parse(array $value, string $string, $default = null)
    {
        $field = explode('.', $string);
        foreach ($field as $v) {
            if(!isset($value[$v])) {
                $value = $default;
            } else if(!is_bool($value[$v]) && empty($value[$v]) && isset($default)) {
                $value = $default;
            } else {
                $value = $value[$v];
            }
        }
        return $value;
    }
}

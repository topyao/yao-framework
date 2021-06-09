<?php
declare(strict_types=1);


namespace Max\Tools;


class Str
{
    public static function parse(array $value, string $string, $default = null)
    {
        $field = explode('.', $string);
        foreach ($field as $v) {
            $value = $value[$v] ?? $default;
        }
        return $value;
    }
}

<?php
declare(strict_types=1);


namespace Max\Tools;


class Str
{
    public static function parse(array $value, string $string, $default = null)
    {
        $field = explode('.', $string);
        foreach ($field as $v) {
            if (isset($value[$v])) {
                if(empty($value[$v]) && isset($default)){
                    $value = $default;
                }else{
                    $value = $value[$v];
                }
            } else {
                $value = $default;
                break;
            }
        }
        return $value;
    }
}

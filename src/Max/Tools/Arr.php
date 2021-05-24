<?php
declare(strict_types=1);


namespace Max\Tools;


class Arr
{
    /**
     * 判断数组是不是关联数组
     * @param array $array
     * @return bool
     */
    public static function isAssoc(array $array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * 判断是否索引数组
     * @param array $array
     * @return bool
     */
    public static function isIndex(array $array)
    {
        return array_keys($array) === range(0, count($array) - 1);
    }

    /**
     * 获取一个关联数组，索引数组的键值对将转换为值=>null对
     * @param array $array
     * @return array
     */
    public static function getAssoc(array $array)
    {
        $return = [];
        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
                $return[$value] = null;
            } else {
                $return[$key] = $value;
            }
        }
        return $return;
    }

    public static function inArray(string $key, array $haystack)
    {
        return isset(array_flip($haystack)[$key]);
    }

    public function toJson(array $array): string
    {
        try {
            // 返回JSON数据格式到客户端 包含状态信息
            $json = json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (false === $json) {
                throw new \Exception(json_last_error_msg());
            }
        } catch (\Exception $e) {
            if ($e->getPrevious()) {
                throw $e->getPrevious();
            }
            throw $e;
        }
        return $json;
    }
}
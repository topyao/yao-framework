<?php

namespace Yao;

/**
 * Class Controller
 * @package Yao
 */
abstract class Controller
{

    /**
     * @param string $class 验证器完整类名
     * @param array $data 验证数据
     * @param array $notice 验证失败提示消息
     */
    protected function validate(string $class = \App\Http\Validate::class, array $data = [], array $notice = [])
    {
        return (new $class($data))->notice($notice);
    }
}

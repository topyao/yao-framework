<?php

namespace Yao\Http\Response;

/**
 * 数组数据响应类[自动转json]
 * Class Json
 * @package Yao\Http\Response
 */
class Json extends \Yao\Http\Response
{

    public $header = ['Content-Type:application/json; charset=UTF-8','X-Powered-By:YaoPHP'];

    protected $options = [
        'json_encode_param' => JSON_UNESCAPED_UNICODE,
    ];

    /**
     * 处理数据
     * @access protected
     * @param mixed $data 要处理的数据
     * @return mixed
     * @throws \Exception
     */
    public function data($data)
    {
        try {
            // 返回JSON数据格式到客户端 包含状态信息
            $res = json_encode($data, $this->options['json_encode_param']);
            if (false === $res) {
                throw new \Exception(json_last_error_msg());
            }
        } catch (\Exception $e) {
            if ($e->getPrevious()) {
                throw $e->getPrevious();
            }
            throw $e;
        }
        $this->data = $res;
        return $this;
    }
}

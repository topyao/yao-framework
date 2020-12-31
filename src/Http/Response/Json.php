<?php

namespace Yao\Http\Response;

class Json extends \Yao\Http\Response
{

    protected $options = [
        'json_encode_param' => JSON_UNESCAPED_UNICODE,
    ];

    protected $contentType = 'application/json';

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
        parent::data($res);
    }

}

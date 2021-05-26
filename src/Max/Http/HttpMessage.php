<?php


namespace Max\Http;


abstract class HttpMessage
{

    /**
     * 取得所有Header
     * @return array
     */
    public function getHeaders()
    {
        return $this->header;
    }

    /**
     * 判断Header是否存在
     * @param string $name
     * @return bool
     */
    public function hasHeader($name)
    {
        return isset($this->header[strtoupper($name)]);
    }

    /**
     * 取得某一个Header
     * @param string $name
     * @return array
     */
    public function getHeader($name)
    {
        $name = strtoupper($name);
        if ($this->hasHeader($name)) {
            $header = [$name, $this->header[$name]];
        }
        return $header ?? [];
    }

    /**
     * 取一行Header
     * @param string $name
     * @return string
     */
    public function getHeaderLine($name)
    {
        $name = strtoupper($name);
        if ($this->hasHeader($name)) {
            $header = "{$name}: {$this->header[$name]}";
        }
        return $header ?? '';
    }
}
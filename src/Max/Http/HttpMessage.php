<?php


namespace Max\Http;


class HttpMessage
{

    public function getHeaders()
    {
        return $this->header;
    }

    public function hasHeader($name)
    {
        return isset($this->header[$name]);
    }

    public function getHeader($name)
    {
        if ($this->hasHeader($name)) {
            $header = [$name, $this->header[$name]];
        }
        return $header ?? [];
    }

    public function getHeaderLine($name)
    {
        if ($this->hasHeader($name)) {
            $header = "{$name}: {$this->header[$name]}";
        }
        return $header ?? '';
    }
}
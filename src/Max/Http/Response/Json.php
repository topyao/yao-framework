<?php
declare(strict_types=1);

namespace Max\Http\Response;

use Max\Exception\InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use JsonSerializable;

/**
 * 数组数据响应类[自动转json]
 * Class Json
 * @package Max\Http\Response
 */
class Json implements StreamInterface
{

    /**
     * @var string
     */
    protected $stream;

    public function __construct($json)
    {
        if (is_string($json)) {
            $this->stream = $json;
        } else if (is_array($json) || $json instanceof JsonSerializable) {
            $this->stream = $this->serialize($json);
        } else {
            throw new InvalidArgumentException('暂不支持的数据类型: ' . gettype($this->stream), 500);
        }
    }

    public function __toString()
    {
        return $this->stream;
    }

    public function close()
    {
        // TODO: Implement close() method.
    }

    public function detach()
    {
        // TODO: Implement detach() method.
    }

    public function getSize()
    {
        return strlen($this->stream);
    }

    public function tell()
    {
        // TODO: Implement tell() method.
    }

    public function eof()
    {
        // TODO: Implement eof() method.
    }

    public function isSeekable()
    {
        // TODO: Implement isSeekable() method.
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        // TODO: Implement seek() method.
    }

    public function rewind()
    {
        // TODO: Implement rewind() method.
    }

    public function isWritable()
    {
        // TODO: Implement isWritable() method.
    }

    public function write($string)
    {
        // TODO: Implement write() method.
    }

    public function isReadable()
    {
        // TODO: Implement isReadable() method.
    }

    public function read($length)
    {
        // TODO: Implement read() method.
    }

    public function getContents()
    {
        // TODO: Implement getContents() method.
    }

    public function getMetadata($key = null)
    {
        // TODO: Implement getMetadata() method.
    }

    public function serialize($var)
    {
        try {
            // 返回JSON数据格式到客户端 包含状态信息
            $json = json_encode($var, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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

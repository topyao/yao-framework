<?php

namespace Yao\Database;

/**
 * 数据集类
 * @property $data
 * @property $query
 * Class Collection
 * @package Yao
 */
class Collection implements \ArrayAccess, \JsonSerializable
{

    /**
     * 最后一个插入的ID
     * @var int|null
     */
    private ?int $lastInsertId = null;

    /**
     * 总数
     * @var int|null
     */
    private ?int $count = null;

    /**
     * DUMP信息
     * @var string|null
     */
    private ?string $dump = null;

    /**
     * SQL
     * @var string
     */
    private $query = '';

    /**
     * 绑定的参数
     * @var array
     */
    private $bindParam = [];

    /**
     * 查询的数据
     * @var array
     */
    private $data = [];

    public function __construct()
    {
    }

    /**
     * 判断数据集是否为空
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->data);
    }

    /**
     * 数据集转json方法
     * @return false|string
     */
    public function toJson()
    {
        return json_encode($this->data);
    }

    /**
     * 数据集转数组方法
     * @return mixed
     */
    public function toArray()
    {
        return $this->data;
    }

    public function offsetExists($offset)
    {
    }


    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }


    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }


    public function offsetUnset($offset)
    {
    }

    /**
     * 可以直接json_encode
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->data;
    }

    public function __set($arg, $value)
    {
        $this->$arg = $value;
    }

    public function __get($arg)
    {
        return $this->$arg ?? null;
    }
}

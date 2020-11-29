<?php

namespace yao;

class Model implements \ArrayAccess
{
    protected ?string $table = null;
    protected static string $pk = 'id';  //设置主键
//    protected $pdo = null;  //存储pdo对象
//    protected static $model = [];

    protected array $data = [];


    public function __construct()
    {

    }

    public function offsetExists($offset)
    {

    }

    public function offsetGet($offset)
    {

    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {

    }

    public static function create(array $data = [])
    {
        $data = $data ?: static::$data;
        return Db::name(static::$table ?? strtolower((new \ReflectionClass(static::class))->getShortName()))->insert($data);
    }

}

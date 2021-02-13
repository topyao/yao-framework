<?php

namespace Yao\Database;

use Yao\Facade\Db;

/**
 * @method where(array $where);
 * @method whereIn(array $whereIn);
 * @method whereLike(array $whereLike);
 * @method whereNull(array $whereNull);
 * @method whereNotNull(array $whereNotNull);
 * @method insert(array $data);
 * @method field(string|array $fields)
 * Class Model
 * @package Yao
 */
class Model
{

    /**
     * 表名
     * @var string|null
     */
    public ?string $name = null;

    /**
     * 默认主键
     * @var string
     */
    public $key = 'id';

    /**
     * 初始化表名
     * Model constructor.
     */
    final public function __construct()
    {
        $this->name = $this->name ?? strtolower(ltrim(strrchr(get_called_class(), '\\'), '\\'));
    }

    /**
     * 模型初始化方法
     * 不要再使用__construct
     */
    public function init()
    {

    }

    /**
     * @param $function_name
     * @param $arguments
     * @return \Yao\Database\Driver
     */
    final public function __call($functionName, $arguments)
    {
        return Db::name($this->name)->$functionName(...$arguments);
    }
}

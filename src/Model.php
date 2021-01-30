<?php

namespace Yao;

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

    public $name = null;

    public function __construct()
    {
        $this->name = $this->name ?? strtolower(ltrim(strrchr(get_called_class(), '\\'), '\\'));
    }

    /**
     * @param $function_name
     * @param $arguments
     * @return \Yao\Db\Driver
     */
    public function __call($functionName, $arguments)
    {
        return Db::name($this->name)->$functionName(...$arguments);
    }
}

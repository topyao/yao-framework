<?php

namespace Yao\Db;

use Yao\Facade\Config;

/**
 * 数据库外部接口
 * Class Db
 * @package yao
 */
class Db
{
    public $driver;
    public array $config = [];

    public function __construct()
    {
        $this->config = Config::get('database');
        $database = $this->config['type'];
        $driver = '\\Yao\\Db\\Drivers\\' . ucfirst($database);
        $this->driver = new $driver();
        $this->collection = new \Yao\Collection();
    }


    /**
     * @param string $sql
     * @param array|null $data
     * @param bool $all
     * @return mixed
     */
    public function query(string $sql, ?array $data = [], bool $all = true)
    {
        return $this->driver->query($sql, $data, $all);
    }

    /**
     * 执行一条操作语句
     * @param string $sql
     * @param array $data
     * @return int
     */
    public function exec(string $sql, array $data = []): int
    {
        return $this->driver->exec($sql, $data);
    }

    /**
     * 通过调用该方法设置数据表并返回实例化对象用于连贯操作
     * @param string $table_name
     * 数据表（和数据库中的完全对应）
     * @return Db|null
     * 返回实例化对象
     */
    public function name(string $table_name)
    {
//        dump($this->driver);
        return $this->driver->name($table_name);
    }
}

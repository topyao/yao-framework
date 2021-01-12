<?php

namespace Yao\Db;

use \PDOException;
use Yao\{
    Facade\Config,
    Traits\SingleInstance
};

/**
 * 数据库操作类
 * Class Query
 * @package Yao\Db
 */
class Query
{
    use SingleInstance;

    const FETCHTYPE = \PDO::FETCH_ASSOC;

    // private ?array $config = [];

    private string $type = '';

    private $PDOstatement;

    private \PDO $pdo;

    public static function instance($dsn,$config)
    {
        if (!static::$instance instanceof static) {
            static::$instance = new static($dsn,$config);
        }
        return static::$instance;
    }

    private function __construct($dsn,$config)
    {
        $this->_connect($dsn,$config);
    }

    /**
     * 数据库连接方法
     * @throws /PDOException
     */
    private function _connect($dsn,$config)
    {
        $this->pdo = new \PDO($dsn, $config['user'], $config['pass'], $config['options']);
    }

    /**
     * 预处理
     * @param $sql
     * @param array $data
     * @return object
     */
    public function prepare(string $sql, array $data = []): \PDOStatement
    {
        $this->PDOstatement = $this->pdo->prepare($sql);
        $this->PDOstatement->execute($data);
        return $this->PDOstatement;
    }

    public function fetchAll($sql, $params, $fetchType = self::FETCHTYPE)
    {
        return $this->prepare($sql, $params)->fetchAll($fetchType);
    }

    public function fetch($sql, $params, $fetchType = self::FETCHTYPE)
    {
        return $this->prepare($sql, $params)->fetch($fetchType);
    }
}
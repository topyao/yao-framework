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

    private ?array $config = [];

    private string $type = '';

    private $PDOstatement;

    private \PDO $pdo;

    public static function instance($dsn)
    {
        if (!static::$instance instanceof static) {
            static::$instance = new static($dsn);
        }
        return static::$instance;
    }

    private function __construct($dsn)
    {
        // Config::load('database');
        $this->type = Config::get('database.type');
        $this->config = Config::get('database.' . $this->type);
        // if (empty($this->config)) {
        //     throw new \Exception('没有找到数据库配置文件');
        // }
        $this->_connect($dsn);
    }

    /**
     * 数据库连接方法
     * @throws /PDOException
     */
    private function _connect($dsn)
    {
        // $dsn = $this->type . ':host=' . $this->config['host'] . ';port=' . $this->config['port'] . ';dbname=' . $this->config['dbname'] . ';charset=' . $this->config['charset'];
        $this->pdo = new \PDO($dsn, $this->config['user'], $this->config['pass'], $this->config['options']);
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
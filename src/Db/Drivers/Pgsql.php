<?php


namespace Yao\Db\Drivers;


use Yao\Db\Driver;

/**
 * Class Pgsql
 * @package Yao\Db\Drivers
 */
class Pgsql extends Driver
{

    /**
     * 数据库字段引号类型
     * @var string
     */
    protected $quote = '';

    /**
     * @param $limit
     * @param null $offset
     * @return $this
     */
    public function limit($limit, $offset = null)
    {
        $this->_setLimit('LIMIT ' . $limit . ($offset ? ' OFFSET ' . $offset : ''));
        return $this;
    }

    public function dsn(): string
    {
        return 'pgsql:host=' . $this->config['host'] . ';port=' . $this->config['port'] . ';dbname=' . $this->config['dbname'] . ';';
    }

}
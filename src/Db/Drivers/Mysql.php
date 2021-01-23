<?php

namespace Yao\Db\Drivers;

use Yao\Db\Driver;

/**
 * Class Mysql
 * @package Yao\Db\Drivers
 */
class Mysql extends Driver
{
    /**
     * 数据库字段引号类型
     * @var string
     */
    protected $quote = '';

    /**
     * Mysql条数限制
     */
    public function limit($limit, $offset = null)
    {
        $this->_setLimit('LIMIT ' . $limit . ($offset ? ',' . $offset : ''));
        return $this;
    }

    public function dsn(): string
    {
        return 'mysql:host=' . $this->config['host'] . ';port=' . $this->config['port'] . ';dbname=' . $this->config['dbname'] . ';charset=' . $this->config['charset'];
    }

}
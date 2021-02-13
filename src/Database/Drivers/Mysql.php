<?php

namespace Yao\Database\Drivers;

use Yao\Database\Driver;

/**
 * Class Mysql
 * @package Yao\Db\Drivers
 */
class Mysql extends Driver
{
    /**
     * Mysql条数限制
     */
    public function limit($limit, $offset = null)
    {
        $this->_setLimit('LIMIT ' . $limit . ($offset ? ',' . $offset : ''));
        return $this;
    }

    /**
     * Mysql PDO-DSN
     * @return string
     */
    public function dsn(): string
    {
        return 'mysql:host=' . $this->config['host'] . ';port=' . $this->config['port'] . ';dbname=' . $this->config['dbname'] . ';charset=' . $this->config['charset'];
    }

}
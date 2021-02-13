<?php


namespace Yao\Database\Drivers;


use Yao\Database\Driver;

/**
 * Class Pgsql
 * @package Yao\Db\Drivers
 */
class Pgsql extends Driver
{

    /**
     * Pgsql数量限制
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
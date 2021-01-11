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
     * Mysql条数限制
     */
    public function limit($limit, $offset = null)
    {
        $this->_setLimit(' LIMIT ' . $limit . ($offset ? ',' . $offset : ''));
        return $this;
    }
}
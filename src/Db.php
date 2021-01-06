<?php

namespace Yao;

use Yao\Db\Query;

/**
 * Class Db
 * @package yao
 */
class Db
{
    //表名
    private string $name;
    private string $field = '*';
    private array $bindParam = [];
    private $collection;
    //存放拼接sql的必要数组
    private array $call = [
        'where' => '',
        'group' => '',
        'order' => '',
        'limit' => ''
    ];

    public function __construct()
    {
        $this->collection = new Collection();
    }

    /** 多条查询
     * @return mixed
     */
    public function select()
    {
        $sql = 'SELECT ' . $this->field . ' FROM ' . $this->name . $this->_condition();
        $res = Query::instance()->prepare($sql, $this->bindParam);
        $this->collection->data = $res->fetchAll(\PDO::FETCH_ASSOC);
        $this->collection->query = $sql;
        return $this->collection;
    }

    public function value()
    {
        $sql = 'SELECT ' . $this->field . ' FROM ' . $this->name . $this->_condition();
        $res = Query::instance()->prepare($sql, $this->bindParam);
        $this->collection->data = $res->fetchAll(PDO::FETCH_ASSOC)[$this->field];
        $this->collection->query = $sql;
        return $this->collection;
    }

    /**
     * 查询单条
     * @return mixed
     */
    public function find()
    {
        $sql = 'SELECT ' . $this->field . ' FROM ' . $this->name . $this->_condition();
        $res = Query::instance()->prepare($sql, $this->bindParam);
        $data = $res->fetch(PDO::FETCH_ASSOC);
        if (false == $data) {
            return false;
        } else {
            $this->collection->data = $data;
            return $this->collection;
        }
    }


    public function update(array $data)
    {
        //拼接预处理sql并添加绑定参数
        $set = '';
        foreach ($data as $field => $value) {
            $set .= $field . ' = ? , ';
            $params[] = $value;
        }
        $set = substr($set, 0, -3);
        //将绑定参数从头部加入到静态属性中
        array_unshift($this->bindParam, ...$params);
        $sql = 'UPDATE ' . $this->name . ' SET ' . $set . $this->_condition();
        return Query::instance()
            ->prepare($sql, $this->bindParam)
            ->rowCount();
    }

    public function insert(array $data)
    {
        $fields = '(' . implode(',', array_keys($data)) . ')';
        $params = '(' . rtrim(str_repeat('?,', count($data)), ',') . ')';
        foreach ($data as $value) {
            $this->bindParam[] = $value;
        }
        $sql = 'INSERT INTO ' . $this->name . ' ' . $fields . ' ' . 'VALUES ' . $params;
        $res = Query::instance()->prepare($sql, $this->bindParam);
        return $this->pdo->lastinsertid();
    }

    /**
     * 删除
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function delete()
    {
        $sql = 'DELETE FROM ' . $this->name . $this->call['where'];
        unset($this->call['where']);
        return Query::instance()
            ->prepare($sql, $this->bindParam)
            ->rowCount();
    }

    /**
     * @param string $sql
     * @param array|null $data
     * @param bool $all
     * @return mixed
     */
    public function query(string $sql, ?array $data = [], bool $all = true)
    {
        $PDOstatement = Query::instance()->prepare($sql, $data);
        return $all ? $PDOstatement->fetchAll(PDO::FETCH_ASSOC) : $PDOstatement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 执行一条操作语句
     * @param string $sql
     * @param array $data
     * @return int
     */
    public function exec(string $sql, array $data = []): int
    {
        return Query::instance()
            ->prepare($sql, $data)
            ->rowCount();
    }

    /**
     * 通过调用该方法设置数据表并返回实例化对象用于连贯操作
     * @param string $table_name
     * 数据表（和数据库中的完全对应）
     * @return Db|null
     * 返回实例化对象
     */
    public function name(string $table_name): Db
    {
        $this->name = $table_name;
        return $this;
    }

    /**
     * 设置查询字段
     * @param string|array $field
     * @return Db
     */
    public function field($field): Db
    {
        $field = is_array($field) ? implode(',', $field) : $field;
        $this->field = '' . $field . '';
        return $this;
    }

    /**
     * where条件表达式，使用数组参数的时候会自动使用预处理
     * @param string|array $where
     * where条件表达式
     * @return Db|null
     */
    public function where($where): Db
    {
        $this->_checkEmpty();
        if (!empty($where)) {
            if (is_string($where)) {
                $this->call['where'] = $this->call['where'] . $where;
            } else if (is_array($where)) {
                foreach ($where as $key => $value) {
                    $this->bindParam[] = $value;
                    $this->call['where'] .= $key . '=? and ';
                }
                $this->call['where'] = substr($this->call['where'], 0, -5);
            }
        }
        return $this;
    }

    /**
     * 模糊查询
     * @param array $like
     * @return Db
     */
    public function whereLike(array $like): Db
    {
        $this->_checkEmpty();
        foreach ($like as $key => $value) {
            $this->call['where'] .= $key . ' LIKE ? AND ';
            $this->bindParam[] = $value;
        }
        $this->call['where'] = substr($this->call['where'], 0, -5);
        return $this;
    }

    public function whereNull(array $field): Db
    {
        $this->_checkEmpty();
        foreach ($field as $key) {
            $this->call['where'] .= $key . ' IS NULL AND ';
        }
        $this->call['where'] = substr($this->call['where'], 0, -5);
        return $this;
    }

    public function whereNotNull(array $field): Db
    {
        $this->_checkEmpty();
        foreach ($field as $key) {
            $this->call['where'] .= $key . ' IS NOT NULL AND ';
        }
        $this->call['where'] = substr($this->call['where'], 0, -5);
        return $this;
    }


    public function whereIn(array $whereIn = [])
    {
        $this->_checkEmpty();
        $condition = '';
        foreach ($whereIn as $column => $range) {
            $bindStr = rtrim(str_repeat('?,', count($range)), ',');
            $condition .= $column . ' in (' . $bindStr . ') AND ';
            array_push($this->bindParam, ...$range);
        }
        $this->call['where'] .= substr($condition, 0, -5);
        return $this;
    }

    /**
     * Mysql条数限制
     * @param mixed ...$limit
     * @return Db|null
     */
    public function limit(...$limit)
    {
        $this->call['limit'] = ' LIMIT ';
        if (count($limit) == 2) {
            $this->call['limit'] .= implode(',', $limit);
        } else {
            $this->call['limit'] .= $limit[0];
        }
        return $this;
    }

    /**
     * order排序操作，支持多字段排序
     * @param array $order
     * 传入数组形式的排序字段，例如['id' => 'desc','name' => 'asc']
     * @return Db|null
     */
    public function order(array $order = [])
    {
        if (!empty($order)) {
            $this->call['order'] = ' order by ';
            foreach ($order as $ord => $by) {
                $this->call['order'] .= $ord . ' ' . $by . ',';
            }
            $this->call['order'] = rtrim($this->call['order'], ',');
        }
        return $this;
    }

    /**
     * group by ... having 可以传入最多两个参数
     * @param mixed ...$group
     * 第一个参数为group字段，第二个为having
     * @return null
     */
    public function group(...$group)
    {
        if (count($group) > 2) {
            throw new \Exception('group传入参数数量不正确');
        }
        if (count($group) == 2) {
            $this->call['group'] = ' group by ' . $group[0] . ' having ' . $group[1];
        } else {
            $this->call['group'] = ' group by ' . $group[0];
        }
        return $this;
    }

    /**
     * 根据$this->call数组生成查询语句
     * @return string
     */
    private function _condition(): string
    {
        return implode(' ', array_filter($this->call));
    }



    // public function transaction(array $transaction)
    // {
    //     $this->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
    //     try {
    //         $this->pdo->beginTransaction(); //开启事务
    //         foreach (func_get_args() as $key => $sql) {
    //             $this->pdo->exec($sql);
    //         }
    //         $this->pdo->commit();
    //     } catch (PDOException $e) {
    //         $this->pdo->rollback();
    //         $this->message = $e->getMessage();
    //         return FALSE;
    //     }
    //     $this->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
    //     return TRUE;
    // }

    /**
     * 检查where属性是否为空并提供拼接的前缀
     */
    private function _checkEmpty()
    {
        if (!isset($this->call['where']) || empty($this->call['where'])) {
            $this->call['where'] = ' WHERE ';
        } else {
            $this->call['where'] .= ' AND ';
        }
    }

//
//    public function __destruct()
//    {
//        $this->name = '';
//        $this->field = '*';
//        $this->bindParam = [];
//        $this->collection = null;
//        $this->call = [];
//    }
}

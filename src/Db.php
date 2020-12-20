<?php

namespace Yao;

use PDO;
use PDOException;
use Yao\Facade\Config;

/**
 * @method array query(string $sql, array $data = [], bool $all = false)
 * @method int exec(string $sql, array $data = [])
 * Class Db
 * @package yao
 */
class Db
{

    //保存存放Db类对象
    private static ?Db $obj = null;

    //配置文件
    private ?array $config = [];

    private string $type = '';

    //pdo实例
    private ?object $link = null;

    //表名
    private static string $name;
    private static string $field = '*';
    private static array $bindParam = [];

    //存放拼接sql的必要数组
    private static array $call = [
        'where' => '',
        'group' => '',
        'order' => '',
        'limit' => ''
    ];

    /**
     * 构造，不能使用new关键字创建对象
     * Db constructor.
     */
    private function __construct()
    {
        $this->type = Config::get('database.type');
        $this->config = Config::get('database.' . $this->type);
        $this->_connect();
        $this->collection = new Collection();
    }

    /**
     * 数据库连接方法
     * @throws /PDOException
     */
    private function _connect()
    {
        $dsn = $this->type . ':host=' . $this->config['host'] . ';port=' . $this->config['port'] . ';dbname=' . $this->config['dbname']; /*. ';charset=' . $this->charset*/
        $this->link = new PDO($dsn, $this->config['user'], $this->config['pass'], $this->config['options']);
    }

    /**
     * ['query','exec']方法静态调用
     * @param $method
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic($method, $args)
    {
        $callable = ['query', 'exec'];
        if (in_array($method, $callable)) {
            return call_user_func_array([self::instance(), '_' . $method], $args);
        }
        throw new \Exception("请求的方法{$method}不能被静态调用");
    }

    /** 多条查询
     * @return mixed
     */
    public function select()
    {
        $collection = new Collection();
        $sql = 'SELECT ' . self::$field . ' FROM ' . self::$name . $this->_condition();
        $res = $this->_prepare($sql, self::$bindParam);
        $collection->data = $res->fetchAll(PDO::FETCH_ASSOC);
        $collection->query = $sql;
        $this->_flush();
        return $collection;
    }


    /**
     * 查询单条
     * @return mixed
     */
    public function find()
    {
        $sql = 'SELECT ' . self::$field . ' FROM ' . self::$name . $this->_condition();
        $res = $this->_prepare($sql, self::$bindParam);
        $data = $res->fetch(PDO::FETCH_ASSOC);
        if (false == $data) {
            return false;
        } else {
            $collection = new Collection();
            $collection->data = $data;
            $this->_flush();
            return $collection;
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
        array_unshift(self::$bindParam, ...$params);
        $sql = 'UPDATE ' . self::$name . ' SET ' . $set . $this->_condition();
        $res = $this->_prepare($sql, self::$bindParam);
        $this->_flush();
        return $res->rowCount();
    }

    public function insert(array $data)
    {
        $fields = '(' . implode(',', array_keys($data)) . ')';
        $params = '(' . rtrim(str_repeat('?,', count($data)), ',') . ')';
        foreach ($data as $value) {
            self::$bindParam[] = $value;
        }
        $sql = 'INSERT INTO ' . self::$name . ' ' . $fields . ' ' . 'VALUES ' . $params;
        $res = $this->_prepare($sql, self::$bindParam);
        $this->_flush();
        return $this->link->lastinsertid();
    }

    /**
     * 删除
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function delete()
    {
        $sql = 'DELETE FROM ' . self::$name . self::$call['where'];
        unset(self::$call['where']);
        $res = $this->_prepare($sql, self::$bindParam);
        $this->_flush();
        return $res->rowCount();
    }

    /**
     * @param string $sql
     * @param array|null $data
     * @param bool $all
     * @return mixed
     */
    private function _query(string $sql, ?array $data = [], bool $all = false)
    {
        $PDOstatement = $this->_prepare($sql, $data);
        $this->_flush();
        return (false === $all) ? $PDOstatement->fetch(PDO::FETCH_ASSOC) : $PDOstatement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 执行一条操作语句
     * @param string $sql
     * @param array $data
     * @return int
     */
    private function _exec(string $sql, array $data = []): int
    {
        $PDOstatement = $this->_prepare($sql, $data);
        $this->_flush();
        return $PDOstatement->rowCount();
    }

    /**
     * 通过调用该方法设置数据表并返回实例化对象用于连贯操作
     * @param string $table_name
     * 数据表（和数据库中的完全对应）
     * @return Db|null
     * 返回实例化对象
     */
    public static function name(string $table_name): Db
    {
        self::$name = $table_name;
        return self::instance();
    }

    /**
     * 设置查询字段
     * @param string|array $field
     * @return Db
     */
    public function field($field): Db
    {
        $field = is_array($field) ? implode(',', $field) : $field;
        self::$field = '' . $field . '';
        return self::$obj;
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
                self::$call['where'] = self::$call['where'] . $where;
            } else if (is_array($where)) {
                foreach ($where as $key => $value) {
                    self::$bindParam[] = $value;
                    self::$call['where'] .= $key . '=? and ';
                }
                self::$call['where'] = substr(self::$call['where'], 0, -5);
            }
        }
        return self::$obj;
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
            self::$call['where'] .= $key . ' LIKE ? AND ';
            self::$bindParam[] = $value;
        }
        self::$call['where'] = substr(self::$call['where'], 0, -5);
        return self::$obj;
    }

    public function whereNull(array $field): Db
    {
        $this->_checkEmpty();
        foreach ($field as $key) {
            self::$call['where'] .= $key . ' IS NULL AND ';
        }
        self::$call['where'] = substr(self::$call['where'], 0, -5);
        return self::$obj;
    }

    public function whereNotNull(array $field): Db
    {
        $this->_checkEmpty();
        foreach ($field as $key) {
            self::$call['where'] .= $key . ' IS NOT NULL AND ';
        }
        self::$call['where'] = substr(self::$call['where'], 0, -5);
        return self::$obj;
    }


    public function whereIn(array $whereIn = [])
    {
        $this->_checkEmpty();
        $condition = '';
        foreach ($whereIn as $column => $range) {
            $bindStr = rtrim(str_repeat('?,', count($range)), ',');
            $condition .= $column . ' in (' . $bindStr . ') AND ';
            array_push(self::$bindParam, ...$range);
        }
        self::$call['where'] .= substr($condition, 0, -5);
        return self::$obj;
    }

    /**
     * Mysql条数限制
     * @param mixed ...$limit
     * @return Db|null
     */
    public function limit(...$limit)
    {
        self::$call['limit'] = ' LIMIT ';
        if (count($limit) == 2) {
            self::$call['limit'] .= implode(',', $limit);
        } else {
            self::$call['limit'] .= $limit[0];
        }
        return self::$obj;
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
            self::$call['order'] = ' order by ';
            foreach ($order as $ord => $by) {
                self::$call['order'] .= $ord . ' ' . $by . ',';
            }
            self::$call['order'] = rtrim(self::$call['order'], ',');
        }
        return self::$obj;
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
            self::$call['group'] = ' group by ' . $group[0] . ' having ' . $group[1];
        } else {
            self::$call['group'] = ' group by ' . $group[0];
        }
        return self::$obj;
    }

    /**
     * 根据self::$call数组生成查询语句
     * @return string
     */
    private function _condition(): string
    {
        $condition = implode(' ', array_filter(self::$call));
        return $condition;
    }

    /**
     * 预处理
     * @param $sql
     * @param array $data
     * @return object
     */
    private function _prepare(string $sql, array $data = []): \PDOStatement
    {
        $PDOstatement = $this->link->prepare($sql);
        empty($data) ? $PDOstatement->execute() : $PDOstatement->execute($data);
        return $PDOstatement;
    }

    /**
     * @return Db
     * 单例模式创建对象
     */
    private static function instance(): Db
    {
        if (!(self::$obj instanceof self)) {
            self::$obj = new self();
        }
        return self::$obj;
    }

    /**
     * 屏蔽克隆对象
     */
    private function __clone()
    {
    }

    // public function transaction(array $transaction)
    // {
    //     $this->link->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
    //     try {
    //         $this->link->beginTransaction(); //开启事务
    //         foreach (func_get_args() as $key => $sql) {
    //             $this->link->exec($sql);
    //         }
    //         $this->link->commit();
    //     } catch (PDOException $e) {
    //         $this->link->rollback();
    //         self::$message = $e->getMessage();
    //         return FALSE;
    //     }
    //     $this->link->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
    //     return TRUE;
    // }


    public function __destruct()
    {
        self::$obj = null;
    }

    /**
     * 检查where属性是否为空并提供拼接的前缀
     */
    private function _checkEmpty()
    {
        if (!isset(self::$call['where']) || empty(self::$call['where'])) {
            self::$call['where'] = ' WHERE ';
        } else {
            self::$call['where'] .= ' AND ';
        }
    }


    private function _flush()
    {
        self::$name = '';
        self::$field = '*';
        self::$bindParam = [];
        self::$call = [
            'where' => '',
            'group' => '',
            'order' => '',
            'limit' => ''
        ];
    }
}

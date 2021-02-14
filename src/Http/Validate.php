<?php

namespace Yao\Http;

/**
 * 验证器类
 * @method Validate max(array $rule)
 * @method Validate length(array $rule)
 * @method Validate bool(array $rule)
 * @method Validate min(array $rule)
 * @method Validate in(array $rule)
 * @method Validate required(array $rule)
 * @method Validate regexp(array $rule)
 * @method Validate confirm(array $rule)
 * @author ：chengyao
 * @version : 0.0.1
 * Class Validate
 * @package Yao
 */
class Validate
{

    /**
     * 批量验证设置
     * @var bool
     */
    protected bool $checkAll = false;

    /**
     * 为true失败抛出异常
     * @var bool
     */
    protected bool $throwAble = false;

    /**
     * 存放用户定义的规则
     * @var array
     */
    protected array $rule = [];

    /**
     * 存放用户需要验证的数据
     * @var array
     */
    protected array $data = [];

    /**
     * 验证失败的消息提示
     * @var array
     */
    protected array $message = [];

    /**
     * 用户自定义提示信息
     * @var array
     */
    protected array $notice = [];


    /**
     * 初始化验证规则
     * Validate constructor.
     * @param array $rule 可以传入验证规则
     */
    public function __construct(array $data = [], array $rule = [])
    {
        $this->data += $data;
        $this->rule += $rule;
    }

    public function notice(array $notice = [])
    {
        $this->notice += $notice;
        return $this;
    }

    /**
     * 规则设置方法，验证前必须设置规则
     * @param array $rule
     * 要求是一个多维数组，数组的键为验证字段，数组的值为验证规则
     * @return Validate
     */
    public function rule(array $rule): Validate
    {
        $this->rule += $rule;
        return $this;
    }

    /**
     * 验证数据设置
     * @param array $data
     * @return $this
     */
    public function data(array $data): Validate
    {
        $this->data += $data;
        return $this;
    }


    /**
     * 验证器主要方法
     * @param array $data 需要验证的参数
     * @param array $notice 验证提示信息
     * @param bool $checkAll 是否批量验证
     * @param bool $throwAble 验证失败是否抛出异常
     * @return true|array 验证成功返回true，批量验证返回数组
     */
    public function check(?array $data = null, ?array $notice = null, ?bool $checkAll = null, ?bool $throwAble = null)
    {
        $checkAll ??= $this->checkAll;
        $throwAble ??= $this->throwAble;
        //必须设置验证规则
        $this->_ruleExistsCheck();
        if (!empty($data)) {
            $this->data($data);
        }
        $this->_processData();
        if (!empty($notice)) {
            $this->notice += $notice;
        }
        //遍历每个字段的规则数组
        foreach ($this->rule as $field => $rule) {
            if (!is_array($rule)) {
                throw new \Exception("{$field}的验证规则{$rule}不符合要求");
            }
            //遍历规则数组
            foreach ($rule as $regulation => $expression) {
                //映射验证方法名
                $funcName = '_check' . ucfirst(trim($regulation));
                //检查方法是否存在于验证器
                if (method_exists($this, $funcName)) {
                    //验证失败返回失败信息并终止遍历
                    $vali = $this->$funcName($field, $expression, $this->data[$field], $regulation);
                    if (!$checkAll && !$vali) {
                        $message = $this->message[0] ?? $field . '的' . $regulation . '规则验证失败';
                        if ($throwAble) {
                            throw new \Exception($message, 403);
                        }
                        return $message;
                    }
                } else {
                    throw new \Exception('验证方法' . $funcName . '不存在', 404);
                }
            }
        }
        //否则返回true
        $message = $this->message ?: true;
        if ($throwAble && true !== $message) {
            throw new \Exception(json_encode($message, 256), 416);
        }
        return $message;
    }

    /**
     * 删除验证字段
     * @param string|array $field
     * 可以传入一个字符串，该字符串即需要删除验证的字段
     * 可以传入一个二维数组,数组的键应该为验证字段，值为需要删除的验证规则数组
     * @return $this
     */
    public function remove($field): Validate
    {
        if (is_string($field) && array_key_exists($field, $this->rule)) {
            unset($this->rule[$field]);
        } else if (is_array($field)) {
            foreach ($field as $key => $value) {
//                if (is_string($value)) {
//                    unset($this->rule[$key][$value]);
//                } else {
                foreach ((array)$value as $v) {
                    unset($this->rule[$key][$v]);
                }
//                }
            }
        } else {
            throw new \Exception(__METHOD__ . '方法传入的参数不符合规范');
        }
        return $this;
    }

    /**
     * 追加验证规则
     * @param array $append
     * @return $this
     */
    public function append(array $append): Validate
    {
        foreach ($append as $key => $value) {
            if (array_key_exists($key, $this->rule)) {
                $this->rule[$key] += $value;
            } else {
                $this->rule[$key] = $value;
            }
        }
        return $this;
    }

    public function __call($method, $params)
    {
        if (!method_exists($this, '_check' . $method)) {
            throw new \Exception("调用的方法{$method}不存在");
        }
        foreach ($params[0] as $field => $rule) {
            $this->rule[$field][$method] = $rule;
        }
        return $this;
    }

    /**
     * 验证是否大于rule中的值
     * @param string $field
     * @param mixed $limit
     * @return bool
     */
    protected function _checkMax(string $field, int $limit, string $data = null, ?string $regulation = null): bool
    {
        if (mb_strlen($data, 'UTF-8') > $limit) {
            $this->message[] = $this->notice[$field][$regulation] ?? $field . '的长度不能大于' . $limit;
            return false;
        }
        return true;
    }


    /**
     * 验证是小于rule中的值
     * @param string $field
     * @param mixed $limit
     * @return bool
     */
    protected function _checkMin(string $field, int $limit, $data, ?string $regulation = null): bool
    {
        if (mb_strlen($data, 'UTF-8') < $limit) {
            $this->message[] = $this->notice[$field][$regulation] ?? $field . '的长度不能小于' . $limit;
            return false;
        }
        return true;
    }


    /**
     * 验证字符串的字符数
     * @param string $field
     * @param $limit
     * @return bool
     */
    protected function _checkLength(string $field, array $limit, $data, ?string $regulation = null): bool
    {
        if (2 !== count($limit)) {
            throw new \Exception('参数不正确');
        }
        $strLength = mb_strlen($data, 'UTF-8');
        if ($strLength > $limit[1] || $strLength < $limit[0]) {
            $this->message[] = $this->notice[$field][$regulation] ?? $field . '的长度应该在[' . $limit[0] . ',' . $limit[1] . ']范围内';
            return false;
        }
        return true;
    }


    /**
     * 判断布尔
     * @param string $field
     * @param $limit
     * @return bool
     */
    protected function _checkBool(string $field, bool $limit, $data, ?string $regulation = null): bool
    {
        $true = ['on', 'yes', 'true', true, 1, '1'];
        $false = ['off', 'no', 'false', false, 0, '0'];
        $limit = $limit ? 'true' : 'false';
        if (in_array($data, $$limit, 1)) {
            return true;
        }
        $this->message[] = $this->notice[$field][$regulation] ?? $field . '不为' . $limit;
        return false;
    }


    /**
     * 验证字段是否存在于数组中
     * @param string $field
     * $return bool
     **/
    protected function _checkIn(string $field, array $limit, $data, ?string $regulation = null): bool
    {
        if (!isset($data)) {
            $this->message[] = $this->notice[$field][$regulation] ?? $field . '没有值';
            return false;
        }
        if (!in_array($data, $limit)) {
            $this->message[] = $this->notice[$field][$regulation] ?? $field . '不在条件范围内';
            return false;
        }
        return true;
    }

    /**
     * 验证不能为空
     * @param $field
     * @param $limit
     * @return bool
     */
    protected function _checkRequired($field, bool $limit = true, $data = null, ?string $regulation = null): bool
    {
        //        if (!isset($data)) {
        //            $data = isset($data[$field]) ? $data[$field] : '';
        //        }
        if (true == $limit) {
            if ('' == $data || [] == $data) {
                $this->message[] = $this->notice[$field][$regulation] ?? $field . '的值不能为空';
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 正则验证
     * @param $field
     * @param $limit
     * @return bool
     */
    protected function _checkRegexp(string $field, string $limit, $data = null, ?string $regulation = null): bool
    {
        if (empty($data)) {
            return true;
        }
        if (!preg_match($limit, $data)) {
            $this->message[] = $this->notice[$field][$regulation] ?? $field . '的规则不符合规范';
            return false;
        }
        return true;
    }

    /**
     * 确认输入验证
     * @param string $field
     * @param string $limit
     * @param $data
     * @return bool
     */
    protected function _checkConfirm(string $field, string $limit, $data, ?string $regulation = null): bool
    {
        if (!isset($this->data[$limit])) {
            $this->message[] = $this->notice[$field][$regulation] ?? $limit . '确认字段不存在';
            return false;
        }
        if ($data == $this->data[$limit]) {
            return true;
        }
        $this->message[] = $this->notice[$field][$regulation] ?? $limit . '和' . $field . '两次输入不一致';
        return false;
    }


    /**
     * 闭包验证，需要传入数据的话只能使用连贯操作
     * @param string $field
     * @param \Closure $func
     * @param array $data
     * @return bool
     */
    protected function _checkFunc(string $field, \Closure $func, $data = [], ?string $regulation = null): bool
    {
        $check = call_user_func_array($func, $data);
        if (true === $check) {
            return true;
        } else {
            $this->message[] = $check;
            return false;
        }
    }

    /**
     * 处理数据
     * 不在rule中的数据全部当作null
     */
    private function _processData()
    {
        foreach ($this->rule as $key => $value) {
            if (!array_key_exists($key, $this->data)) {
                $this->data[$key] = null;
            }
        }
    }

    /**
     * 检查是否设置验证规则
     * @throws \Exception
     */
    private function _ruleExistsCheck()
    {
        if ([] === $this->rule) {
            throw new \Exception('未设置验证规则，请先使用rule方法设置验证规则', 400);
        }
    }
}

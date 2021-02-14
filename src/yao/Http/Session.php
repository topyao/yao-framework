<?php

namespace Yao\Http;


class Session
{
    use \Yao\Traits\Parse;

    /**
     * 初始化session
     * Session constructor.
     */
    public function __construct()
    {
        isset($_SESSION) || session_start();
    }

    /**
     * session获取方法
     * @param $name
     * @return array|mixed|string|null
     */
    public function get($name)
    {
        return $this->parse($_SESSION, $name);
    }

    /**
     * session设置方法
     * @param string $name
     * @param $value
     */
    public function set(string $name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * session销毁
     */
    public function destroy()
    {
        $_SESSION = [];
        setcookie(session_name(), null, -1);
        session_destroy();
    }

    /**
     * 检查session闪存
     */
    public function flashCheck()
    {
        if (true === $this->get('yao_session_flash_flag')) {
            $this->set('yao_session_flash_flag', false);
        } else if (false === $this->get('Yao_session_flash_flag')) {
            $this->set('Yao_session_flash_flag', null);
            $this->set($this->get('Yao_session_flash_name'), null);
        }
    }

    /**
     * session闪存设置
     * @param $name
     * @param null $value
     * @return array|mixed|string|null
     */
    public function flash($name, $value = null)
    {
        if (isset($value)) {
            $this->set('Yao_session_flash_flag', true);
            $this->set('Yao_session_flash_name', $name);
            $this->set($name, $value);
        } else {
            return $this->get($name);
        }
    }
}

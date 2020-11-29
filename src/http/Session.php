<?php

namespace yao\http;

class Session
{

    public function __construct()
    {
        isset($_SESSION) || session_start();
    }


    public function get($name)
    {
        return getMultidimensionalArrayValue($_SESSION, $name);
    }


    public function set(string $name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public function destroy()
    {
        $_SESSION = [];
        setcookie(session_name(), null, -1);
        session_destroy();
    }

    public function flashCheck()
    {
        if (true === $this->get('yao_session_flash_flag')) {
            $this->set('yao_session_flash_flag', false);
        } else if (false === $this->get('yao_session_flash_flag')) {
            $this->set('yao_session_flash_flag', null);
            $this->set($this->get('yao_session_flash_name'), null);
        }
    }

    public function flash($name, $value = null)
    {
        if (isset($value)) {
            $this->set('yao_session_flash_flag', true);
            $this->set('yao_session_flash_name', $name);
            $this->set($name, $value);
        } else {
            return $this->get($name);
        }
    }
}

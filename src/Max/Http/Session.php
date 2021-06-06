<?php
declare(strict_types=1);

namespace Max\Http;

use Max\Foundation\App;
use Max\Tools\Str;

class Session
{
    /**
     * 容器实例
     * @var
     */
    protected $app;

    /**
     * 初始化session
     * Session constructor.
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        if ($app->config->get('app.auto_start', false)) {
            $this->init();
        }
    }

    public function init()
    {
        isset($_SESSION) || session_start();
        $this->flashCheck();
        return $this;
    }

    /**
     * session获取方法
     * @param $name
     * @return array|mixed|string|null
     */
    public function get($name)
    {
        return Str::parse($_SESSION, $name);
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
     * 判断session是否存在
     * @param string $key
     * @return bool
     */
    public function has(string $key)
    {
        return !is_null(Str::parse($_SESSION, $key));
    }

    /**
     * session销毁
     */
    public function destroy()
    {
        $_SESSION = [];
        setcookie(session_name(), '', -1);
        session_destroy();
    }

    /**
     * 检查session闪存
     */
    public function flashCheck()
    {
        if (true === $this->get('max_session_flash_flag')) {
            $this->set('max_session_flash_flag', false);
        } else if (false === $this->get('max_session_flash_flag')) {
            $this->set('max_session_flash_flag', null);
            $this->set($this->get('max_session_flash_name'), null);
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
            $this->init()->set('max_session_flash_flag', true);
            $this->set('max_session_flash_name', $name);
            $this->set($name, $value);
        } else {
            return $this->get($name);
        }
    }
}

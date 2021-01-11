<?php


namespace Yao\Traits;

/**
 * 单例Trait
 * Trait SingleInstance
 * @package Yao\Traits
 */
trait SingleInstance
{
    private static $instance;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function instance()
    {
        if (!static::$instance instanceof static) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function __destruct()
    {
        self::$instance = null;
    }
}
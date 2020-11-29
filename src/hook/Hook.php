<?php

namespace yao\hook;

class Hook
{

    private static $hook = [];

    public function __construct()
    {

    }

    public function listen($hookClass)
    {

    }

    public function hook($hookClass, $args)
    {
        if (!isset(self::$hook[$hookClass])) {
            self::$hook[$hookClass] = new $hookClass;
        }
        if (array_key_exists($hookClass, self::$hook)) {
            return self::$hook[$hookClass]->hook($args);
        }
    }
}
<?php

namespace Yao\Facade;

use Yao\Facade;

class Cache extends Facade
{

    protected static function getFacadeClass()
    {
        return \Yao\Cache\Setter::class;
    }
}

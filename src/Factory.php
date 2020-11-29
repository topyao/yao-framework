<?php

namespace yao;

class Factory
{

    public static function class($class, ...$param)
    {
        return new $class(...$param);
    }
}

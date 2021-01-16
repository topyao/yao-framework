<?php


namespace Yao\Cache;

use Yao\Traits\SingleInstance;

abstract class Driver
{
    use SingleInstance;

    public function get(string $key)
    {
    }
}

<?php

namespace Yao\Http\Response;

use Yao\Response;

class Json extends Response
{
    public function __invoke($args)
    {
        echo $args;
        echo 1;
    }
}

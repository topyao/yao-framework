<?php

namespace yao\http\response;

use yao\Response;

class Json extends Response
{
    public function __invoke($args)
    {
        echo $args;
        echo 1;
    }
}

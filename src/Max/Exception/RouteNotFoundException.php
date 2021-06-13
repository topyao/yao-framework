<?php
declare(strict_types=1);

namespace Max\Exception;

class RouteNotFoundException extends HttpException
{

    public function __construct()
    {
        parent::__construct('Page no found.', 404);
    }

}
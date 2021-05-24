<?php
declare(strict_types=1);

namespace Max\Contracts;

interface Middleware
{
    public function handle($request, \Closure $next);
}
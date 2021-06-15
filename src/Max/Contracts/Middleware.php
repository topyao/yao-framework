<?php
declare(strict_types=1);

namespace Max\Contracts;

use Max\Http\Request;

interface Middleware
{
    public function handle(Request $request, \Closure $next);
}
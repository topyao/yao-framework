<?php

namespace Max\Contracts;

use Max\Http\Request;

interface Middleware
{
    public function handle(Request $request, \Closure $next);
}
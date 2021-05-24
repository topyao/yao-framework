<?php

namespace Max\Http\Middleware;

use Max\Foundation\App;

class GlobalCross
{
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        return app('response')->withHeader('Access-Control-Allow-Origin', '*');
    }
}
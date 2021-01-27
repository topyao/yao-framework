<?php


namespace Yao\Http;


class Middleware
{

    public $middleware = [];

    public function handle($request, \Closure $next)
    {

    }


    public function make()
    {

    }


    public function before()
    {

    }


    public function after()
    {

    }

    public function middleware($request, \Closure $closure)
    {

    }

    public function set($middleware)
    {
        $this->middleware = $middleware;
    }
}
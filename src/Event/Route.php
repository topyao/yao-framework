<?php


namespace Yao\Event;

class Route
{
    public function boot()
    {
        \Yao\Facade\Route::match();
    }
}
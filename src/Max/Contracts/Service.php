<?php

namespace Max\Contracts;

interface Service
{
    public function register();

    public function boot();
}
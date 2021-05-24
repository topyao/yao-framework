<?php

namespace Max\Lang;

class Lang
{

    protected $language;

    public function __construct()
    {

    }

    public function import($language = 'zh')
    {
        $this->language = include env('root_path') . '/vendor/max/framework/src/Max/Lang/packages/' . strtolower($language) . '.php';
    }

    public function out(string $keyword, ...$vars)
    {
        $text = $this->language[$keyword] ?? $keyword;
        return sprintf($text, ...$vars);
    }

}
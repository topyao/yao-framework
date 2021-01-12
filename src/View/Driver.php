<?php


namespace Yao\View;


use Yao\Traits\SingleInstance;

abstract class Driver
{

    use SingleInstance;

    protected array $config = [];
    protected string $module = '';
    protected $template_suffix = 'html';


    private function __construct()
    {
        $this->config = config('view');
    }


    protected function _parseModule($template)
    {
        if (strpos($template, '@')) {
            $dir = explode('@', $template);
            $this->module = $dir[0] . DIRECTORY_SEPARATOR;
            $template = $dir[1];
        }
        return $template;
    }

    abstract public function render($template, $arguments = []);
}
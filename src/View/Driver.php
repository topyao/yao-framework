<?php


namespace Yao\View;

use Yao\Facade\Config;
use Yao\Traits\SingleInstance;

abstract class Driver
{

    use SingleInstance;

    protected array $config = [];
    protected string $module = '';
    protected $suffix = 'html';


    protected string $templateDir;

    public static function instance($template)
    {
        if (!static::$instance instanceof static) {
            static::$instance = new static($template);
        }
        return static::$instance;
    }

    private function __construct($template)
    {
        $this->config = Config::getByType('view');
        $this->template = $this->getTemplate($template);
    }


    public function getTemplate($template)
    {
        if (strpos($template, '@')) {
            $dir = explode('@', $template);
            $this->module = $dir[0] . DIRECTORY_SEPARATOR;
            $template = $dir[1];
        }
        $this->templateDir = ROOT . 'views' . DIRECTORY_SEPARATOR . $this->module;
        return $template . '.' . $this->config['template_suffix'] ?: $this->suffix;
    }

    abstract public function render($arguments = []);
}

<?php


namespace Yao\View;

use Yao\Facade\Config;
use Yao\Traits\SingleInstance;

abstract class Driver
{

    use SingleInstance;

    protected const SUFFIX = 'html';

    protected array $config = [];

    public static function instance($template)
    {
        if (!static::$instance instanceof static) {
            static::$instance = new static($template);
        }
        return static::$instance;
    }

    final private function __construct($template)
    {
        $this->config = Config::getByType('view');
        $this->template = str_replace('/', DIRECTORY_SEPARATOR, $template) . '.' . $this->config['suffix'] ?: self::SUFFIX;
    }

    abstract public function render($arguments = []);
}

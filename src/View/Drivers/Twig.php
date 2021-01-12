<?php

namespace Yao\View\Drivers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Yao\View\Driver;

class Twig extends Driver
{
    private $twig;
    private $config = [];
    private $module = '';
    private $template_suffix = 'html';

    private function _setOptions()
    {
        $loader = new FilesystemLoader(ROOT . 'app' . DIRECTORY_SEPARATOR . ucfirst($this->module) . 'View');
        $this->twig = new Environment($loader, [
            'debug' => $this->config['debug'],
            'cache' => $this->config['cache'] ? $this->config['cache_dir'] : false,
        ]);
    }

    public function render($template, $arguments = [])
    {
        if (strpos($template, '@')) {
            $dir = explode('@', $template);
            $this->module = $dir[0] . DIRECTORY_SEPARATOR;
            $template = $dir[1];
        }
        $this->_setOptions();
        return $this->twig->render($template . '.' . $this->config['template_suffix'] ?: $this->template_suffix, $arguments);
    }

}
<?php

namespace Yao\View\Drivers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Yao\View\Driver;

class Twig extends Driver
{
    private $twig;

    private function _setOptions()
    {
        $loader = new FilesystemLoader($this->templateDir);
        $this->twig = new Environment($loader, [
            'debug' => $this->config['debug'],
            'cache' => $this->config['cache'] ? $this->config['cache_dir'] : false,
        ]);
    }

    public function render($arguments = [])
    {
        $this->_setOptions();
        return $this->twig->render($this->template, $arguments);
    }
}

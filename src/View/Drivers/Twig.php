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
        $loader = new FilesystemLoader(ROOT . 'views' . DIRECTORY_SEPARATOR . $this->module);
        $this->twig = new Environment($loader, [
            'debug' => $this->config['debug'],
            'cache' => $this->config['cache'] ? $this->config['cache_dir'] : false,
        ]);
    }

    public function render($template, $arguments = [])
    {
        $template = $this->_parseModule($template);
        $this->_setOptions();
        return $this->twig->render($template, $arguments);
    }
}

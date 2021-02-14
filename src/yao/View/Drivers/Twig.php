<?php

namespace Yao\View\Drivers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Yao\View\Driver;

class Twig extends Driver
{

    /**
     * Twig实例
     * @var Environment
     */
    private Environment $twig;

    private function _setOptions()
    {
        $loader = new FilesystemLoader(env('views_path'));
        $this->twig = new Environment($loader, [
            'debug' => $this->config['debug'],
            'cache' => $this->config['cache'] ? env('cache_path') . 'view' : false,
        ]);
    }

    public function render($arguments = [])
    {
        $this->_setOptions();
        return $this->twig->render($this->template, $arguments);
    }
}

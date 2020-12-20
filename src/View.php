<?php

namespace Yao;

class View
{

    private $twig;
    private $config = [];
    private $module = '';

    public function __construct()
    {
        $this->config = \Yao\Facade\Config::get('view');
    }

    private function _setOptions()
    {
        $loader = new \Twig\Loader\FilesystemLoader(ROOT . 'app' . DIRECTORY_SEPARATOR . $this->module . 'view');
        $this->twig = new \Twig\Environment($loader, [
            'debug' => $this->config['debug'],
            'cache' => $this->config['cache'] ? $this->config['cache_dir'] : false,
        ]);
    }

    public function fetch($template, $arguments = [])
    {
        if (strpos($template, '@')) {
            $dir = explode('@', $template);
            $this->module = $dir[0] . DIRECTORY_SEPARATOR;
            $template = $dir[1];
        }
        $this->_setOptions();
        return $this->twig->render($template . '.' . $this->config['template_suffix'], $arguments);
    }

}
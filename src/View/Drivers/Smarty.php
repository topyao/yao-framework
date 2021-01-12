<?php

namespace Yao\View\Drivers;

use Yao\View\Driver;

class Smarty extends Driver
{
    private $smarty = null;

    private function _setOptions()
    {
        if (!($this->smarty instanceof \Smarty)) {
            $this->smarty = new \Smarty();
        }
        $this->smarty->debugging = $this->config['debug'];
        $this->smarty->caching = $this->config['cache'];
        $this->smarty->left_delimiter = $this->config['left_delimiter'];
        $this->smarty->right_delimiter = $this->config['right_delimiter'];
        $this->smarty
            ->setTemplateDir(ROOT . 'app' . DIRECTORY_SEPARATOR . ucfirst($this->module) . DIRECTORY_SEPARATOR . 'View')
            ->setCompileDir(ROOT . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'compile')
            ->setCacheDir($this->config['cache_dir']);
    }


    public function render($template, $arguments = [])
    {
        $template = $this->_parseModule($template);
        if ([] !== $arguments) {
            foreach ($arguments as $key => $value) {
                $this->smarty->assign($key, $value);
            }
        }
        $this->_setOptions();
        return $this->smarty->display($template . '.' . $this->config['template_suffix'] ?: $this->template_suffix);
    }
}
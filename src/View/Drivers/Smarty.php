<?php

namespace Yao\View\Drivers;

use Yao\View\Driver;

class Smarty extends Driver
{

    /**
     * Smarty实例
     * @var \Smarty
     */
    private \Smarty $smarty;

    /**
     * Smarty配置
     */
    private function _setOptions()
    {
        $this->smarty = new \Smarty();
        $this->smarty->debugging = $this->config['debug'];
        $this->smarty->caching = $this->config['cache'];
        $this->smarty->left_delimiter = $this->config['left_delimiter'];
        $this->smarty->right_delimiter = $this->config['right_delimiter'];
        $this->smarty
            ->setTemplateDir(env('views_path'))
            ->setCompileDir(env('cache_path') . 'view' . DIRECTORY_SEPARATOR . 'compile')
            ->setCacheDir(env('cache_path') . 'view');
    }

    /**
     * @param array $arguments
     * @return mixed|void
     * @throws \SmartyException
     */
    public function render($arguments = [])
    {
        $this->_setOptions();
        foreach ($arguments as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        return $this->smarty->display($this->template);
    }
}

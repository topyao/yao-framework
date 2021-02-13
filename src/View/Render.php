<?php
declare(strict_types=1);

namespace Yao\View;

use Yao\App;

class Render
{

    /**
     * 容器实例
     * @var App
     */
    protected App $app;

    public string $driver = '';

    public function __construct(App $app)
    {
        $this->app = $app;
        if (!class_exists($this->driver = $app->config->get('view.type'))) {
            $this->driver = 'Yao\\View\\Drivers\\' . ucfirst($this->driver);
        }
    }

    /**
     * 模板渲染方法
     * @param $template
     * 模板名
     * @param array $arguments
     * 参数列表
     * @return mixed
     */
    public function render($template, $arguments = [])
    {
        return $this->app->invokeMethod([$this->driver, 'render'], [$arguments], true, [$template]);
    }
}

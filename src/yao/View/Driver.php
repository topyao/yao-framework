<?php
declare(strict_types=1);

namespace Yao\View;

use Yao\Config;

/**
 * Class Driver
 * @package Yao\View
 */
abstract class Driver
{

    /**
     * 默认模板后缀
     */
    protected const SUFFIX = 'html';

    /**
     * View配置
     * @var array|mixed
     */
    protected array $config = [];

    /**
     * 处理后的模板名
     * @var string
     */
    protected $template;

    /**
     * Driver constructor.
     * @param $template
     * 需要渲染的模板
     * @param Config $config
     */
    public function __construct($template, Config $config)
    {
        $this->config = $config->getByType('view');
        $this->template = str_replace('/', DIRECTORY_SEPARATOR, $template) . '.' . $this->config['suffix'] ?: self::SUFFIX;
    }

    /**
     * 驱动实现方法
     * @param array $arguments
     * 给模板传递的参数
     * @return mixed
     */
    abstract public function render($arguments = []);
}

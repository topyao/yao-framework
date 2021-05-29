<?php
declare(strict_types=1);

namespace Max\Console;

abstract class Command
{

    protected $name = '';

    protected $description = '';

    protected $arguments = [];

    abstract public function out();

    /**
     * 初始话命令信息
     */
    public function configure()
    {
        $this->setName('name')->setDescription('description');
    }

    /***
     * 设置命令描述
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * 设置命令名
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function addArgument(string $argument)
    {
        $this->arguments[$argument];
    }

    protected function writeLine(string $format, string ...$args)
    {
        echo sprintf($format, $args);
    }
}
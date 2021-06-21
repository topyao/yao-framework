<?php
declare(strict_types=1);

namespace Max;

class Log extends \Psr\Log\AbstractLogger
{

    /**
     * Config实例
     * @var Config
     */
    protected $config;

    protected $logs = [];


    public function write()
    {
        if (true == config('app.log') && !empty($this->logs)) {
            foreach ($this->logs as $level => $log) {
                file_put_contents($this->getPath($level) . date('d') . '.log', $log, FILE_APPEND);
                unset($this->logs[$level]);
            }
        }
    }

    public function log($level, $message, array $context = array())
    {
        //TODO 高版本语法$this->logs[$level] ??= ''
        if (!isset($this->logs[$level])) {
            $this->logs[$level] = '';
        }
        $this->logs[$level] .= $this->separator(50) . sprintf("[%s] %s: %s\n", date('Y-m-d H:i:s'), $message, json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    protected function separator($number): string
    {
        return str_repeat('-', $number) . "\n";
    }

    public function getPath($level)
    {
        $path = env('storage_path') . 'logs' . DIRECTORY_SEPARATOR . $level . DIRECTORY_SEPARATOR . date('Ym') . DIRECTORY_SEPARATOR;
        \Max\Tools\File::mkdir($path);
        return $path;
    }

    public function __destruct()
    {
        $this->write();
    }

}
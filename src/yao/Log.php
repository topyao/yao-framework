<?php
declare(strict_types=1);

namespace Yao;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log
{

    private $logFile = '';
    private $logName = 'system';
    private Logger $monolog;

    public function __construct()
    {

    }

    public function write($logName, $message, $level = 'warning', array $context = [])
    {
        if (false == \Yao\Facade\Config::get('app.log')) {
            return;
        }
        $logLevel = constant(Logger::class . '::' . strtoupper($level));
        $this->logFile = env('storage_path') . 'logs' . DIRECTORY_SEPARATOR . $logName . DIRECTORY_SEPARATOR . date('Ym') . DIRECTORY_SEPARATOR . date('d') . '.log';
        // create a log channel
        $this->monolog = new Logger($logName);
        $this->monolog->pushHandler(new StreamHandler($this->logFile, $logLevel));

        // add records to the log
        $this->monolog->$level($message, $context);
//        $log->error('Bar');
    }

}
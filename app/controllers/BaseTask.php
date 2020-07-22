<?php


namespace app\controllers;


use framework\Command;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class BaseTask extends Command
{


    /**
     *
     * 获取日志记录者
     *
     * @param $channelName
     * @param string $fileName
     * @param int $logType
     * @return Logger
     * @throws \Exception
     */

    protected function getLogger($channelName, $logType = Logger::DEBUG, $fileName = 'debug.log')
    {
        $logger = new Logger($channelName);
        $handler = new StreamHandler(LOG_PATH . $fileName);

        $formatter = new LineFormatter("%datetime% %channel%:%level_name% %message% %context% %extra%" . PHP_EOL, "Y-m-d H:i:s", false, true);

        $handler->setFormatter($formatter);
        $handler->setLevel($logType);

        $logger->pushHandler($handler);

        return $logger;
    }

}
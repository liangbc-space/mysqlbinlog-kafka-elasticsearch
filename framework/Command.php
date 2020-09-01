<?php

namespace framework;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Connection;

class Command
{


    /**
     * @param string $connName
     * @return Connection
     */

    final protected function getDb($connName = '')
    {

        $connName = $connName ? $connName : Application::$dbConnName;

        return Manager::connection($connName);
    }


    /**
     *
     * fork多个进程
     *
     * @param $pcntlNum
     * @param callable $callable    function($pid, $index){}
     */

    protected function pcntlLoop($pcntlNum, callable $callable)
    {
        if (!extension_loaded('pcntl')) {
            exit('请先安装多进程pcntl扩展' . PHP_EOL);
        }

        $pids = [];
        for ($i = 1; $i <= $pcntlNum; $i++) {

            $pids[$i] = pcntl_fork();

            if ($pids[$i] == -1) {
                die('fork error');
            } elseif ($pids[$i]) {
                //pcntl_wait($status, WNOHANG);
            } else {
                $pid = posix_getpid();

                $callable($pid, $i);

                echo "进程【" . posix_getppid() . "------>{$pid}】退出" . PHP_EOL;
                exit($pid);
            }
        }

        //  等待子进程都退出后退出
        foreach ($pids as $pid) {
            pcntl_waitpid($pid, $status);
        }
    }

}
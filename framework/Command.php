<?php

namespace framework;

class Command
{
    /** @var DbalConnection $mysql */
    public static $mysql;


    /**
     * @return DbalConnection
     */

    final protected function getDb()
    {
        return self::$mysql;
    }


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
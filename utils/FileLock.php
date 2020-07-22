<?php

namespace utils;


use framework\Command;

/**
 *
 * 文件锁
 *
 * Class FileLock
 * @package utils
 */
class FileLock
{


    public $filePath;

    public $fp;

    public function __construct(Command $class)
    {
        $rootPath = ROOT_PATH . 'lockFile/';

        $prefix = "task_";
        $route = $_SERVER['argv'][1];

        $fileName = $prefix . str_replace('/', '_', $route) . '.lock';

        if (!file_exists($rootPath) && !mkdir($rootPath, 0777, true))
            throw new \Exception("ERROR_CREATE_DIR");

        $this->filePath = $rootPath . $fileName;

        $this->fp = @fopen($this->filePath, "a+");
    }


    public function createLock()
    {
        if ($this->fp === false)
            return false;

        return flock($this->fp, LOCK_EX | LOCK_NB);
    }


    public function destroyLock()
    {
        @flock($this->fp, LOCK_UN);    // 释放锁定

        @fclose($this->fp);
        @unlink($this->filePath);

        clearstatcache();

    }


    /**
     *
     * 销毁文件锁时通过创建文件所的方式看是否存在其他进程占用进程锁
     * 如果创建成功时则代表无其他进程占用文件锁直接释放文件锁
     * 反之存在即不释放文件锁等待占用文件锁的进程自行释放文件锁
     *
     */

    public function __destruct()
    {
        if ($this->createLock())
            $this->destroyLock();
    }
}
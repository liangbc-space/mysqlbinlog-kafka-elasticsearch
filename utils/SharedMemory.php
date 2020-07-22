<?php

namespace utils;

/**
 * linux下/dev/shm内存缓存
 *
 * Class SharedMemory
 */
class SharedMemory
{

    private $shm_id;


    public function __construct($shm_key = null, $shm_size = 1024)
    {
        $shm_key = !$shm_key ? ftok(__FILE__, 'a') : $shm_key;
        $shm_size = bcmul($shm_size, 1024, 0);

        if (!function_exists('shm_attach')) {
            throw new \Exception('请先安装sysvshm扩展');
        }

        $this->shm_id = shm_attach($shm_key, $shm_size);
    }

    /**
     * 设置共享内存值，想不通变量赋值，后者会替换前者
     * @param number|string $key
     * @param $value
     *
     * @return bool
     */
    public function set($key, $value)
    {
        return shm_put_var($this->shm_id, $this->initKey($key), $value);
    }

    /**
     * 获取共享内存中变量的值
     * @param number|string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return shm_get_var($this->shm_id, $this->initKey($key));
    }

    /**
     * 检测共享内存中变量是否存在
     * @param number|string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return shm_has_var($this->shm_id, $this->initKey($key));
    }

    /**
     * 移除共享内存中指定变量
     * @param number|string $key
     *
     * @return bool
     */
    public function remove($key)
    {
        return shm_remove_var($this->shm_id, $this->initKey($key));
    }

    /**
     * 删除共享内存块
     * @return bool
     */
    public function delete()
    {
        return shm_remove($this->shm_id);
    }

    /**
     *
     * 初始化key   因为该系列的key紧支持number，不支持字符串
     *
     * @param number|string $key
     * @return float|int|string
     * @throws Exception
     */
    private function initKey($key)
    {
        if (is_numeric($key))
            return $key;
        elseif (is_string($key))
            return hexdec(bin2hex($key));
        else {
            throw new \Exception('参数key是不支持的类型');
        }
    }
}
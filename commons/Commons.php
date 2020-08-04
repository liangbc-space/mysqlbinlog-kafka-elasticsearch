<?php


namespace commons;


class Commons
{

    /**
     *
     * 获取毫秒时间戳
     *
     * @return float
     */

    public static function getMsecTimestamp()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }


    public static function stringToInteger($input)
    {

        if (is_string($input) && is_numeric($input)) {
            $output = intval($input);
        } elseif (is_array($input)) {
            $output = array_map(function ($val) {
                return intval($val);
            }, $input);
        } else
            $output = $input;

        return $output;
    }


    /**
     *
     * 对象或数组对象转普通数组
     *
     * @param array|object $input
     * @return array
     */

    public static function object2Array($input)
    {
        if (!is_object($input) && !is_array($input))
            return $input;

        is_object($input) && $input = get_object_vars($input);

        return array_map(function ($item) {

            return (is_object($item) || is_array($item)) ? self::object2Array($item) : $item;

        }, $input);

    }

}
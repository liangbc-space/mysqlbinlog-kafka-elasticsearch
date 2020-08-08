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


    /**
     *
     * 数据转int类型   支持字符串、普通数组、多维数组
     *
     * @param $input
     * @return array|int|int[]
     */

    public static function toInteger($input)
    {
        if (is_array($input))
            return array_map(function ($val) {
                return self::toInteger($val);
            }, $input);

        if (is_numeric($input) && ceil($input) == $input)
            return intval($input);
        else
            return $input;

    }


    /**
     *
     * 数据转字符串    支持字符串、普通数组、多维数组
     *
     * @param $input
     * @return array|string|string[]
     */

    public static function toString($input)
    {
        if (is_array($input))
            return array_map(function ($val) {
                return self::toString($val);
            }, $input);

        if (is_numeric($input) || is_string($input) || is_null($input))
            return strval($input);
        else
            return $input;

    }


    /**
     *
     * 转小数（double）   支持字符串、普通数组、多维数组
     *
     * @param $input
     * @return array|float|float[]
     */

    public static function toDecimal($input)
    {
        if (is_array($input))
            return array_map(function ($val) {
                return self::toDecimal($val);
            }, $input);

        if (is_numeric($input))
            return doubleval($input);
        else
            return $input;
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
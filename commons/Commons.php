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

}
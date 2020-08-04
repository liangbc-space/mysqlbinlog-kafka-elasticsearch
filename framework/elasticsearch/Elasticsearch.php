<?php


namespace framework\elasticsearch;


interface Elasticsearch
{

    /**
     *
     * 获取index
     *
     * @return mixed
     */

    public static function getIndex();


    /**
     *
     * 获取alias
     *
     * @return mixed
     */

    public static function getAlias();

}
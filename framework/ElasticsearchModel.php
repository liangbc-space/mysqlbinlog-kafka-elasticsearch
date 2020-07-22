<?php

namespace framework;


use Elasticsearch\Client;


abstract class ElasticsearchModel
{

    /** @var Client */
    private static $elasticsearch;

    /** @var string es中index的名称或index前缀名称 */
    protected static $index;


    protected function __construct()
    {
        self::getDb();
    }


    /**
     * @return Client
     */

    public static function getDb()
    {
        if (!self::$elasticsearch instanceof ElasticsearchClient) {
            $elasticsearchConnOptions = require CONFIG_PATH . 'elasticsearch.config.php';
            self::$elasticsearch = ElasticsearchClient::getInstance($elasticsearchConnOptions);
        }


        return self::$elasticsearch;
    }


    /**
     *
     * 获取index
     *
     * @return mixed
     */

    abstract static function getIndex();

}
<?php

namespace framework\elasticsearch;


use Elasticsearch\Client;


abstract class ElasticsearchModel implements Elasticsearch
{

    /** @var Client */
    private static $elasticsearch;

    /** @var string es中index的名称或index前缀名称 */
    protected static $index;

    /** @var string es中index绑定的别名名称，注意合理使用alias和index进行操作 */
    protected static $alias;


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
     * 过滤无用数据
     *
     * @param array $result
     * @param $count
     * @return array|mixed
     */

    public static function _getSource(array $result, &$count = 0)
    {
        if (!isset($result['hits']['hits']))
            return $result;

        $count = $result['hits']['total']['value'];
        $result = $result['hits']['hits'];

        return $result;
    }


    /**
     *
     * 过滤分组无用数据
     *
     * @param array $result
     * @return array|mixed
     */

    public static function _getAggs(array $result)
    {
        $aggs = isset($result['aggregations']) ? $result['aggregations'] : [];

        $aggs = array_map(function ($items) {
            return array_column($items['buckets'], 'doc_count', 'key');
        }, $aggs);

        return $aggs;
    }

}
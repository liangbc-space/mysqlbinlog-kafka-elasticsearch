<?php

namespace framework;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

/**
 *
 * Class Elasticsearch
 *
 */
class ElasticsearchClient
{

    /** @var array */
    private static $connOption;

    /** @var Client */
    private static $client;

    private static $retries = 3;

    private static $connectionPoolModel = '\Elasticsearch\ConnectionPool\StaticNoPingConnectionPool';

    private function __construct()
    {
    }


    public static function setRetries($retries = 3)
    {
        self::$retries = $retries;
    }

    public static function setConnectionPoolModel($namespace = '\Elasticsearch\ConnectionPool\StaticNoPingConnectionPool')
    {
        self::$connectionPoolModel = $namespace;
    }


    public static function getInstance($connOptions)
    {
        if (!defined('JSON_PRESERVE_ZERO_FRACTION')) {
            define('JSON_PRESERVE_ZERO_FRACTION', 1024);
        }

        try {
            if (!self::$client instanceof Client) {

                self::$client = ClientBuilder::create()
                    ->setHosts($connOptions)
                    ->setConnectionPool(self::$connectionPoolModel, [])
                    ->setSelector('\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector')
                    ->setRetries(self::$retries)
                    ->build();

            }

        } catch (\Exception $e) {
            die('elasticsearch初始化失败');
        }

        return self::$client;
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }


    public function __call($name, $params)
    {
        return self::$client->$name(...$params);
    }

}
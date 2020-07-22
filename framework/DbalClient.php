<?php

namespace framework;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;


class DbalClient
{

    /** @var Connection $instance */
    private static $instance;

    private function __construct()
    {

    }


    /**
     *
     * @param array $connOption
     * @return Connection
     * @throws DBALException
     */

    public static function getInstance(array $connOption = [])
    {

        if (self::$instance instanceof Connection)
            return self::$instance;

        $defaultConnOptions = require CONFIG_PATH . 'db.config.php';

        $charset = 'utf8';
        if (isset($connOption['charset']) && $connOption['charset'])
            $charset = $connOption['charset'];
        elseif (isset($defaultConnOptions['charset']) && $defaultConnOptions['charset'])
            $charset = $defaultConnOptions['charset'];

        $connectionOptions = array(
            'host' => isset($connOption['db_host']) && $connOption['db_host'] ? $connOption['db_host'] : $defaultConnOptions['db_host'],
            'port' => isset($connOption['db_port']) && $connOption['db_port'] ? $connOption['db_port'] : $defaultConnOptions['db_port'],
            'user' => isset($connOption['db_username']) && $connOption['db_username'] ? $connOption['db_username'] : $defaultConnOptions['db_username'],
            'password' => isset($connOption['db_password']) && $connOption['db_password'] ? $connOption['db_password'] : $defaultConnOptions['db_password'],
            'dbname' => isset($connOption['db_name']) && $connOption['db_name'] ? $connOption['db_name'] : $defaultConnOptions['db_name'],
            'driver' => 'pdo_mysql',
            'charset' => $charset,
        );

        $config = new Configuration();

        self::$instance = DriverManager::getConnection($connectionOptions, $config);

        return self::$instance;
    }


    private function __clone()
    {
        // TODO: Implement __clone() method.
    }


}
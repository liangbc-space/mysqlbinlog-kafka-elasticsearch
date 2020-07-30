#!/usr/bin/env php
<?php

/**
 *
 * 脚本入口文件
 *
 * 脚本访问方式是通过命名空间和类名访问的;
 * @example     php cmd.php dataSync/Elasticsearch/test -name=lbc
 *
 * @author lbc346093@163.com
 *
 */


if (version_compare(PHP_VERSION, '5.6.0', '<')) exit('require PHP > 5.3.0 !');

if (strtolower(php_sapi_name()) !== 'cli') exit('请在CLI模式下运行');


define('ROOT_PATH', dirname(__FILE__) . '/');

define('DEBUG', true);


if (!isset($_SERVER['argv'][1])) exit('错误，请填写正确的类名' . PHP_EOL);

require_once(ROOT_PATH . 'framework/Application.php');


$cmd = new \framework\Application();
$cmd->run();

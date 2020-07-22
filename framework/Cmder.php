<?php

namespace framework;

defined('APP_PATH') or define('APP_PATH', ROOT_PATH . 'app/');

defined('CONTROLLER_PATH') or define('CONTROLLER_PATH', APP_PATH . 'controllers/');

defined('CONFIG_PATH') or define('CONFIG_PATH', ROOT_PATH . 'config/');

defined('LOG_PATH') or define('LOG_PATH', ROOT_PATH . 'logs/');

defined('DEBUG') or define('DEBUG', false);
if (!DEBUG) {
    ini_set('error_reporting', E_ERROR);

    ini_set('display_errors', 'Off');
}


class Cmder
{

    public function __construct()
    {
        $dbConfigPath = CONFIG_PATH . 'db.config.php';
        if (!file_exists($dbConfigPath) || !is_file($dbConfigPath))
            throw new \Exception('请先依据config/db.config.php.example 创建db.config.php配置文件');

        require ROOT_PATH . 'vendor/autoload.php';

        Command::$mysql = DbalClient::getInstance();
    }


    /** @var \ReflectionObject */
    static $refObj;

    public function run()
    {

        $classnameOpts = explode('/', trim($_SERVER['argv'][1], '/'));

        $classPrefix = str_replace(ROOT_PATH, '', CONTROLLER_PATH);
        $classPrefix = array_filter(explode('/', $classPrefix));
        array_unshift($classnameOpts, ...$classPrefix);

        $methodName = end($classnameOpts);
        array_pop($classnameOpts);

        $count = count($classnameOpts);
        $classnameOpts[$count - 1] = ucfirst($classnameOpts[$count - 1]);

        $classnameOpts = array_map(function ($item) {
            $item = trim($item, '/');
            $item = trim($item, '\\');
            return $item;
        }, $classnameOpts);

        self::$refObj = new \ReflectionClass(implode('\\', $classnameOpts));

        if (!self::$refObj->hasMethod($methodName))
            throw new \Exception('错误的调用，不存在' . $methodName . '方法');

        $args = $this->getMethodArgs($methodName);

        self::$refObj->newInstance()->$methodName(...$args);
    }


    private function getMethodArgs($methodName)
    {
        $classMethod = self::$refObj->getMethod($methodName);

        $methodParams = $classMethod->getParameters();

        $args = [];
        $params = $this->parseParams();
        foreach ($methodParams as $key => $param) {
            $paramName = $param->getName();

            if (isset($params["-{$paramName}"])) {
                $args[] = $params["-{$paramName}"];
            } elseif (!$param->isDefaultValueAvailable()) {
                throw new \Exception('错误的调用，' . $methodName . '方法的必传参数' . $paramName . '不存在');
            }

        }

        return $args;
    }


    private function parseParams()
    {
        $args = [];

        foreach ($_SERVER['argv'] as $key => $val) {
            if (in_array($key, [0, 1]))
                continue;

            parse_str($val, $arg);

            $args = array_merge($args, $arg);
        }

        return $args;
    }

}
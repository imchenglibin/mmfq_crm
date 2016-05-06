<?php
use Phalcon\Mvc\Application;
use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

define('APP_PATH', realpath('..') . '/');

try {
  date_default_timezone_set('Asia/Shanghai');
  //errorCode
  require_once(APP_PATH . "app/config/error.php");
  //config
  require_once(APP_PATH . "app/config/config.php");
  $config = new Config($settings);
  //loader
  require_once(APP_PATH . "app/config/loader.php");
  //di
  require_once(APP_PATH . "app/config/di.php");
  $application = new Application($di);
  echo $application->handle()->getContent();
} catch(Exception $e) {
  if ($config->debug) {
    echo $e->getMessage();
  }
}

?>

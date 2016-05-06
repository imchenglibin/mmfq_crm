<?php
use Phalcon\Loader;
$loader = new Loader();
$loader->registerDirs(array(
      $config->application->controllersDir,
      $config->application->modelsDir,
      $config->application->servicesDir,
      $config->application->validatorsDir,
      $config->application->pluginsDir
  ))->register();
 ?>

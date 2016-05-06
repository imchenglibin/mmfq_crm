<?php
use Phalcon\Session\Adapter\Files as Session;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Router\Annotations as RouterAnnotations;
use Phalcon\Mvc\View;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Config;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Dispatcher;

$di = new FactoryDefault();

$di->set('session', function () {
  $session = new Session();
  $session->start();
  return $session;
});

$di->set('router', function() {
  $router = new RouterAnnotations(false);
  $router->addResource('Users', '/api/users');
  $router->addResource('Customers', '/api/customers');
  $router->addResource('Projects', '/api/projects');
  $router->addResource('ReturnVisitRecords', '/api/return_visit_records');
  $router->addResource('ProjectKinds', '/api/project_kinds');
  $router->addResource('Statistics', '/api/statistics');
  $router->addResource('Error', '/api/error');
  return $router;
});

$di->set('view', function () use($config) {
  $view = new View();
  $view->setViewsDir($config->application->viewsDir);
  return $view;
});

$di->set('mmfqError', function() {
  return new Config(include(APP_PATH . "app/config/error.php"));
});

$di->set('db', function () use ($config) {
  return new DbAdapter(
    array(
      "host"     => $config->database->host,
      "username" => $config->database->username,
      "password" => $config->database->password,
      "dbname"   => $config->database->dbname
    )
  );
});

/**
 * We register the events manager
 */
$di->set('dispatcher', function () use ($di) {

	$eventsManager = new EventsManager;

	/**
	 * Check if the user is allowed to access certain action using the SecurityPlugin
	 */
	$eventsManager->attach('dispatch:beforeDispatch', new SecurityPlugin);

	/**
	 * Handle exceptions and not-found exceptions using NotFoundPlugin
	 */
	$eventsManager->attach('dispatch:beforeException', new NotFoundPlugin);

	$dispatcher = new Dispatcher;
	$dispatcher->setEventsManager($eventsManager);

	return $dispatcher;
});
?>

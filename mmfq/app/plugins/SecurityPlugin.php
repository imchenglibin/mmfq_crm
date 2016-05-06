<?php

use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl\Adapter\Memory as AclList;

/**
 * SecurityPlugin
 *
 * This is the security plugin which controls that users only have access to the modules they're assigned to
 */
class SecurityPlugin extends Plugin
{
	/**
	 * This action is executed before execute any action in the application
	 *
	 * @param Event $event
	 * @param Dispatcher $dispatcher
	 * @return bool
	 */
	public function beforeDispatch(Event $event, Dispatcher $dispatcher)
	{
    $controller = strtolower($dispatcher->getControllerName());
    $action = strtolower($dispatcher->getActionName());
    $userId = $this->session->get('user_id');
    $user = Users::findFirstById($userId);

    $publicControllersAndActions = array(
      'users' => array('login'),
      'error' => array('forbiddenerror', 'authenticationerror', 'notfounderror')
    );

    $isPublic = false;
    foreach ($publicControllersAndActions as $key => $value) {
      if ($key == $controller) {
        foreach ($value as $vvalue) {
          if ($vvalue == $action) {
            $isPublic = true;
          }
        }
      }
    }

    if (!$isPublic) {
      if (!$user) {
        	$dispatcher->forward(array(
    				'controller' => 'error',
    				'action'     => 'forbiddenError'
    			));
          return false;
      }
    }
	}
}

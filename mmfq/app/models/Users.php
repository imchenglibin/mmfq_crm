<?php
use Phalcon\Mvc\Model;
/**
 *
 */
class Users extends Model
{
  public $id;
  public $user_name;
  public $password;
  public $real_name;
  public $role;

  public function getSource() {
    return "users";
  }

  public static function getUser($username, $password) {
    return Users::findFirst(
      array(
        'user_name = :username: and password = :password:',
        'bind' => array('username' => $username, 'password' => md5($password))
      )
    );
  }

  public static function getUsers() {
    $users = Users::find();
    $usersJson = array();
    foreach ($users as $user) {
      $usersJson[] = $user;
    }
    return $usersJson;
  }

  public static function addUser($username, $password, $realName, $role) {
    $user = new Users();
    $user->user_name = $username;
    $user->password = md5($password);
    $user->real_name = $realName;
    $user->role = $role;

    if ($user->save()) {
      return $user;
    } else {
      return false;
    }
  }
}

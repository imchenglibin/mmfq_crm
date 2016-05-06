<?php
/**
 * @RoutePrefix("/api/users")
 */
class UsersController extends ControllerBase
{
  /**
   * @Route("/login", methods={"POST"})
   */
  public function loginAction() {
    $username = $this->request->getPost('username');
    $password = $this->request->getPost('password');

    if (!UsersValidator::validateUsername($username)) {
      return $this->returnJson(null, $this->mmfqError->invalidUsername);
    }

    if(!UsersValidator::validtePassword($password)) {
      return $this->returnJson(null, $this->mmfqError->invalidPassword);
    }

    $user = Users::getUser($username, $password);
    if ($user) {
      $this->saveLogInUser($user);
      return $this->returnJson($user);
    } else {
      return $this->returnJson(null, $this->mmfqError->logInFailed);
    }
  }

  /**
   * @Route("/logout", methods={"POST"})
   */
   public function logOutAction() {
     $this->saveLogInUser(null);
     return $this->returnJson(null);
   }

  /**
   * @Route("/add_user", methods={"POST"})
   */
  public function addUserAction() {
    $logInUser = $this->getLogInUser();
    if ($logInUser->role != 'admin') {
      return $this->returnJson(null, $this->mmfqError->forbidded);
    }

    $username = $this->request->getPost('username');
    $password = $this->request->getPost('password');
    $realName = $this->request->getPost('real_name');
    $role = 'employee';

    if (!UsersValidator::validateUsername($username)) {
      return $this->returnJson(null, $this->mmfqError->invalidUsername);
    }

    if(!UsersValidator::validtePassword($password)) {
      return $this->returnJson(null, $this->mmfqError->invalidPassword);
    }

    if (!UsersValidator::validteRealName($realName)) {
      return $this->returnJson(null, $this->mmfqError->invalidRealName);
    }

    $existsUser = Users::findFirstByUserName($username);
    if ($existsUser) {
      return $this->returnJson(null, $this->mmfqError->userExists);
    }

    $user = Users::addUser($username, $password, $realName, $role);
    if ($user) {
      return $this->returnJson($user);
    } else {
      return $this->returnJson(null, $this->mmfqError->serverInternalError);
    }
  }

  /**
   * @Route("/get_user_info", methods={"GET"})
   */
  public function getUserInfoAction() {
    $user = $this->getLogInUser();
    if ($user) {
      return $this->returnJson($user);
    }
    return $this->returnJson(null, $this->mmfqError->authenticationFailed);
  }

  /**
   * @Route("/update_user_info", methods={"POST"})
   */
   public function updateUserInfoAction() {
     $userId = $this->request->getPost('user_id');
     $user = Users::findFirstById($userId);
     if ($user) {
       $password = $this->request->getPost('password');
       if (strlen($assword) > 0) {
         if (!UsersValidator::validtePassword($password)) {
           return $this->returnJson(null, $this->mmfqError->invalidPassword);
         } else {
           $user->password = md5($password);
         }
       }
       if ($user->save()) {
         return $this->returnJson(null);
       } else {
         return $this->returnJson(null, $this->mmfqError->serverInternalError);
       }
     } else {
       return $this->returnJson(null, $this->mmfqError->userNotExists);
     }
   }

   /**
    * @Route("/update_user_role", methods={"POST"})
    */
   public function updateUserRoleAction() {
     $userId = $this->request->getPost('user_id');
     $role = $this->request->getPost('role');

     if ($role != 'admin' || $role != 'employee') {
       return $this->returnJson(null, $this->mmfqError->invalidParams, '(customer_id)');
     }

     $logInUser = $this->getLogInUser();
     if (!$logInUser || $logInUser->role != 'admin') {
       return $this->returnJson(null, $this->mmfqError->forbidded);
     }

     $user = Users::findFirstById($userId);
     if ($user) {
       $user->role = $role;
       if ($user->save()) {
         return $this->returnJson($user);
       } else {
         return $this->returnJson(null, $this->mmfqError->serverInternalError);
       }
     } else {
       return $this->returnJson(null, $this->mmfqError->userNotExists);
     }
   }

   /**
    * @Route("/get_users", methods={"GET"})
    */
    public function getUsersAction() {
      $logInUser = $this->getLogInUser();
      if ($logInUser->role == 'admin') {
        return $this->returnJson(Users::getUsers());
      } else {
        return $this->returnJson(null, $this->mmfqError->forbidded);
      }
    }

    /**
     * @Route("/delete_user", methods={"POST"})
     */
     public function deleteUserAction() {
       $logInUser = $this->getLogInUser();
       if (!$logInUser || $logInUser->role != 'admin') {
         return $this->returnJson(null, $this->mmfqError->forbidded);
       }
       $userId = $this->request->getPost('user_id');
       $user = Users::findFirstById($userId);
       if ($user) {
         $this->modelsManager->executeQuery("UPDATE Customers SET Customers.user_id=:newUserId: WHERE Customers.user_id=:oldUserId:",
          array(
            'newUserId' => $logInUser->id,
            'oldUserId' => $userId
          )
         );
         if($user->remove()) {
           return $this->returnJson(null);
         } else {
           return $this->returnJson(null, $this->mmfqError->serverInternalError);
         }
       } else {
         return $this->returnJson(null, $this->mmfqError->userNotExists);
       }
     }
}
?>

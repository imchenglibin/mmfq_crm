<?php
/**
 *
 */
class ValidateBase {
  public function validtePassword($password) {
    return strlen($password) >= 6 && strlen($password) <= 20;
  }

  public function validateUsername($username) {
    return strlen($username) > 0 && strlen($username) <= 20;
  }
}

 ?>

<?php
/**
 *
 */
class UsersValidator extends ValidateBase
{
  public static function validteRealName($realName) {
    return strlen($realName) > 0 && strlen($realName) <= 20;
  }

  public static function validteTelephone($telephone) {
    return preg_match("/^1\d{10}$/", $telephone);
  }
}

?>

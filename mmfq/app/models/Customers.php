<?php
use Phalcon\Mvc\Model;
/**
 *
 */
class Customers extends Model
{
  public $id;
  public $user_id;
  public $name;
  public $sign_date;
  public $telephone;
  public $school;
  public $age;
  public $star;
  public $user_real_name;

  public function getSource() {
    return "customers";
  }

  public static function addCustomer($user_id, $user_real_name, $name, $sign_date, $telephone, $school, $age) {
    $customer = new Customers();
    $customer->user_real_name = $user_real_name;
    $customer->user_id = $user_id;
    $customer->name = $name;
    $customer->sign_date = date('Y-m-d H:i:s', $sign_date);
    $customer->telephone = $telephone;
    $customer->school = $school;
    $customer->age = $age;
    $customer->star = 0;
    if ($customer->save()) {
      return $customer;
    } else {
      return false;
    }
  }

  public static function getCustomers($userId) {
    $customers = Customers::find(
      array (
        'user_id = :userId:',
        'bind' => array('userId' => $userId),
        'order' => 'star DESC, sign_date DESC'
      )
    );
    $returnJsons = array();
    foreach ($customers as $customer) {
      $returnJsons[] = $customer;
    }
    return $returnJsons;
  }

  public static function getAllCustomers() {
    $customers = Customers::find(
      array (
        'order' => 'star DESC, sign_date DESC'
      )
    );
    $returnJsons = array();
    foreach ($customers as $customer) {
      $returnJsons[] = $customer;
    }
    return $returnJsons;
  }

  public static function changeCustomerUserId($customerId, $userId) {
    $customer = Customers::findFirst(
      array (
        'id = :orderId:',
        'bind' => array ('orderId' => $customerId)
      )
    );
    if ($customer) {
      $customer->user_id = $userId;
      if ($customer->save()) {
        return $customer;
      }
    }
    return false;
  }
}

 ?>

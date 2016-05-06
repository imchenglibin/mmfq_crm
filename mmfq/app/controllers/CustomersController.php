<?php
/**
 * @RoutePrefix("/api/customers")
 */
class CustomersController extends ControllerBase
{
  /**
   * @Route("/add_customer", methods={"POST"})
   */
  public function addCustomerAction() {
    $name = $this->request->getPost('name');
    $sign_date = $this->request->getPost('sign_date');
    $telephone = $this->request->getPost('telephone');
    $school = $this->request->getPost('school');
    $age = $this->request->getPost('age');

    $user = $this->getLogInUser();
    $user_id = $user->id;
    $user_real_name = $user->real_name;

    if (!UsersValidator::validteRealName($name)) {
      return $this->returnJson(null, $this->mmfqError->invalidRealName);
    }

    if (!isset($sign_date)) {
      return $this->returnJson(null, $this->mmfqError->invalidParams, '(sign_date)');
    }

    if (!isset($age)) {
      return $this->returnJson(null, $this->mmfqError->invalidParams, '(age)');
    }

    if (strlen($school) == 0) {
      return $this->returnJson(null, $this->mmfqError->invalidParams, '(school)');
    }

    if (!UsersValidator::validteTelephone($telephone)) {
      return $this->returnJson(null, $this->mmfqError->invalidParams, '(telephone)');
    }

    $customer = Customers::addCustomer($user_id, $user_real_name, $name, $sign_date, $telephone, $school, $age);
    if($customer) {
      return $this->returnJson($customer);
    } else {
      return $this->returnJson(null, $this->mmfqError->serverInternalError);
    }
  }

  /**
   * @Route("/get_customers", methods={"GET"})
   */
  public function getCustomersAction() {
    $user = $this->getLogInUser();
    if ($user->role == 'admin') {
      $customers = Customers::getAllCustomers();
      return $this->returnJson($customers);
    } else {
      $customers = Customers::getCustomers($user->id);
      return $this->returnJson($customers);
    }
  }

  /**
   * @Route("/get_customer", methods={"GET"})
   */
  public function getCustomerAction() {
    $user = $this->getLogInUser();
    $customerId = $this->request->get('customer_id');
    $customer = Customers::findFirstById($customerId);
    if (!$customer) {
      return $this->returnJson(null, $this->mmfqError->customerNotExists);
    } else {
      if ($customer->user_id != $user->id) {
        return $this->returnJson(null, $this->mmfqError->forbidded);
      } else {
        return $this->returnJson($customer);
      }
    }
  }

  /**
   * @Route("/change_customer_user_id", methods={"POST"})
   */
  public function changeCustomerUserIdAction() {
    $user = $this->getLogInUser();
    if ($user->role != 'admin') {
      return $this->returnJson(null, $this->mmfqError->forbidded);
    }

    $userId = $this->request->getPost('user_id');
    $customerId = $this->request->getPost('customer_id');

    if (!isset($userId)) {
      return $this->returnJson(null, $this->mmfqError->invalidParams, '(user_id)');
    }

    if (!isset($customerId)) {
      return $this->returnJson(null, $this->mmfqError->invalidParams, '(customer_id)');
    }

    $customer = Customers::findFirstById($customerId);
    if (!$customer) {
      return $this->returnJson(null, $this->mmfqError->customerNotExists);
    }

    $user = Users::findFirstById($userId);
    if ($user) {
      $customer = Customers::changeCustomerUserId($customerId, $userId);
      if ($customer) {
        return $this->returnJson($customer);
      } else {
        return $this->returnJson(null, $this->mmfqError->serverInternalError);
      }
    } else {
      return $this->returnJson(null, $this->mmfqError->userNotExists);
    }
  }

  /**
   * @Route("/toggle_star", methods={"POST"})
   */
  public function toggleStarAction() {
    $customerId = $this->request->getPost('customer_id');
    if (!isset($customerId)) {
      return $this->returnJson(null, $this->mmfqError->invalidParams, '(customer_id)');
    }
    $user = $this->getLogInUser();
    $customer = Customers::findFirstById($customerId);
    if ($customer) {
      if ($customer->user_id != $user->id) {
        return $this->returnJson(null, $this->mmfqError->forbidded);
      } else {
        $customer->star = ($customer->star + 1) % 2;
        if ($customer->save()) {
          return $this->returnJson($customer);
        } else {
          return $this->returnJson(null, $this->mmfqError->serverInternalError);
        }
      }
    } else {
      return $this->returnJson(null, $this->mmfqError->customerNotExists);
    }
  }

  /**
   * @Route("/update_customer_info", methods={"POST"})
   */
  public function updateCustomerInfoAction() {
    $customerId = $this->request->getPost('customer_id');
    $name = $this->request->getPost('name');
    $sign_date = $this->request->getPost('sign_date');
    $telephone = $this->request->getPost('telephone');
    $school = $this->request->getPost('school');
    $age = $this->request->getPost('age');

    $customer = Customers::findFirstById($customerId);
    $logInUser = $this->getLogInUser();

    if ($customer->user_id != $logInUser->id) {
      return $this->returnJson(null, $this->mmfqError->forbidded);
    } else {
      if (UsersValidator::validteRealName($name)) {
        $customer->name = $name;
      }

      if (isset($sign_date)) {
        $customer->sign_date = date('Y-m-d H:i:s', $sign_date);;
      }

      if (UsersValidator::validteTelephone($telephone)) {
        $customer->telephone = $telephone;
      }

      if (isset($school)) {
        $customer->school = $school;
      }

      if (isset($age)) {
        $customer->age = $age;
      }

      if ($customer->save()) {
        return $this->returnJson($customer);
      } else {
        return $this->returnJson(null, $this->mmfqError->serverInternalError);
      }
    }
  }

  /**
   * @Route("/get_customers_of_no_return_visit", methods={"GET"})
   */
   public function getCustomersOfNoReturnVisitAction() {
     $untilDate = date('Y-m-d H:i:s', $this->request->get('until_date'));

     $logInUser = $this->getLogInUser();
     $hql = "SELECT Customers.* FROM Customers, ReturnVisitRecords WHERE Customers.user_id=:userId: AND ReturnVisitRecords.is_return = 0 AND ReturnVisitRecords.return_date <= :returnDate: AND ReturnVisitRecords.customer_id = Customers.id";
     $customers = $this->modelsManager->executeQuery($hql, array('returnDate' => $untilDate, 'userId' => $logInUser->id));

     $customersJson = array();
     foreach ($customers as $customer) {
       if (isset($customersJson[$customer->id])) {
         $tmp = $customersJson[$customer->id];
         $tmp->return_visit_records_count += 1;
       } else {
         $customer->return_visit_records_count = 1;
         $customersJson[$customer->id] = $customer;
       }
     }
     return $this->returnJson(array_values($customersJson));
   }

   /**
    * @Route("/delete_customer", methods={"POST"})
    */
    public function deleteCustomerAction() {
      $customerId = $this->request->getPost('customer_id');
      $customer = Customers::findFirstById($customerId);
      if ($customer) {
        $logInUser = $this->getLogInUser();
        if ($customer->user_id != $loginUser->id) {
          return $this->returnJson(null, $this->mmfqError->forbidded);
        } else {
          if ($customer->remove()) {
            $projects = Projects::find(array('customer_id = :customerId:', 'bind' => array('customerId' => $customerId)));
            foreach ($projects as $project) {
              $project->remove();
            }
            $returnVisitRecords = ReturnVisitRecords::find(array('customer_id = :customerId:', 'bind' => array('customerId' => $customerId)));
            foreach ($returnVisitRecords as $returnVisitRecord) {
              $returnVisitRecord->remove();
            }
            return $this->returnJson(null);
          } else {
            return $this->returnJson(null, $this->mmfqError->serverInternalError);
          }
        }
      } else {
        return $this->returnJson(null, $this->mmfqError->customerNotExists);
      }
    }
}
?>

<?php
/**
 * @RoutePrefix("/api/return_visit_records")
 */
class ReturnVisitRecordsController extends ControllerBase
{
  /**
   * @Route("/add_return_visit_record", methods={"POST"})
   */
  public function addReturnVisitRecordAction() {
    $user = $this->getLogInUser();
    $customerId = $this->request->getPost('customer_id');
    $returnDate = $this->request->getPost('return_date');
    $detail = $this->request->getPost('detail');

    if (!isset($customerId)) {
      return $this->returnJson(null, $this->mmfqError->invalidParams, '(customer_id)');
    }

    if (!isset($returnDate)) {
      return $this->returnJson(null, $this->mmfqError->invalidParams, '(return_date)');
    }

    $customer = Customers::findFirstById($customerId);
    if ($customer) {
      if ($customer->user_id != $user->id) {
        return $this->returnJson(null, $this->mmfqError->forbidded);
      }
      $returnVisitRecord = ReturnVisitRecords::addReturnVisitRecord($customerId, $returnDate, $detail);
      if ($returnVisitRecord) {
        return $this->returnJson($returnVisitRecord);
      } else {
        return $this->returnJson(null, $this->mmfqError->serverInternalError);
      }
    } else {
      return $this->returnJson(null, $this->mmfqError->returnVisitRecordNotExists);
    }
  }

  /**
   * @Route("/get_return_visit_records", methods={"GET"})
   */
  public function getReturnVisitRecordsAction() {
    $user = $this->getLogInUser();
    $customerId = $this->request->get('customer_id');
    if (!isset($customerId)) {
      return $this->returnJson(null, $this->mmfqError->invalidParams, '(customer_id)');
    }
    $customer = Customers::findFirstById($customerId);
    if ($customer) {
      if ($customer->user_id != $user->id) {
        return $this->returnJson(null, $this->mmfqError->forbidded);
      }
      return $this->returnJson(ReturnVisitRecords::getReturnVisitRecords($customerId));
    } else {
      return $this->returnJson(null, $this->mmfqError->returnVisitRecordNotExists);
    }
  }

  /**
   * @Route("/update_return_visit_record", methods={"POST"})
   */
   public function updateReturnVisitRecordAction() {
     $recordId = $this->request->getPost('return_visit_record_id');
     $detail = $this->request->getPost('detail');
     if (!isset($detail) || strlen($detail) == 0) {
       return $this->returnJson(null, $this->mmfqError->invalidParams, '(detail)');
     }
     $record = ReturnVisitRecords::findFirstById($recordId);

     if ($record) {
       $customer = Customers::findFirstById($record->customer_id);
       $logInUser = $this->getLogInUser();
       if ($customer->user_id != $logInUser->id) {
         return $this->returnJson(null, $this->mmfqError->forbidded);
       } else {
         $record->detail = $detail;
         $record->is_return = 1;
         if ($record->save()) {
           return $this->returnJson($record);
         } else {
           return $this->returnJson(null, $this->mmfqError->serverInternalError);
         }
       }
     } else {
       return $this->returnJson(null, $this->mmfqError->returnVisitRecordNotExists);
     }
   }

   /**
    * @Route("/delete_return_visit_record", methods={"POST"})
    */
    public function deleteReturnVisitRecordAction() {
      $recordId = $this->request->getPost('return_visit_record_id');
      $record = ReturnVisitRecords::findFirstById($recordId);
      if ($record) {
        $customer = Customers::findFirstById($record->customer_id);
        $logInUser = $this->getLogInUser();
        if ($customer->user_id != $logInUser->id) {
          return $this->returnJson(null, $this->mmfqError->forbidded);
        } else {
          if ($record->remove()) {
            return $this->returnJson(null);
          } else {
            return $this->returnJson(null, $this->mmfqError->serverInternalError);
          }
        }
      } else {
        return $this->returnJson(null, $this->mmfqError->returnVisitRecordNotExists);
      }
    }
}
?>

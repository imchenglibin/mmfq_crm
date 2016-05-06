<?php
use Phalcon\Mvc\Model;
/**
 *
 */
class ReturnVisitRecords extends Model
{
  public $id;
  public $customer_id;
  public $create_date;
  public $return_date;
  public $is_return;
  public $detail;

  public function getSource() {
    return 'return_visit_records';
  }

  public static function addReturnVisitRecord($customerId, $returnDate, $detail) {
    $returnVisitRecord = new ReturnVisitRecords();
    $returnVisitRecord->customer_id = $customerId;
    $returnVisitRecord->return_date = date('Y-m-d H:i:s', $returnDate);
    $returnVisitRecord->create_date = date('Y-m-d H:i:s', time());
    $returnVisitRecord->detail = $detail;

    if (isset($detail) && strlen($detail) > 0) {
      $returnVisitRecord->is_return = 1;
    } else {
      $returnVisitRecord->is_return = 0;
    }

    if ($returnVisitRecord->save()) {
      return $returnVisitRecord;
    } else {
      return false;
    }
  }

  public static function getReturnVisitRecords($customerId) {
    $returnVisitRecords = ReturnVisitRecords::find(
      array (
        'customer_id = :customerId:',
        'bind' => array ('customerId' => $customerId),
        'order' => 'is_return, return_date DESC'
      )
    );

    $returnVisitRecordsJson = array();
    foreach ($returnVisitRecords as $returnVisitRecord) {
      $returnVisitRecordsJson[] = $returnVisitRecord;
    }

    return $returnVisitRecordsJson;
  }
}

?>

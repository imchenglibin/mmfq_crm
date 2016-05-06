<?php
use Phalcon\Mvc\Model;
/**
 *
 */
class Projects extends Model
{
  public $id;
  public $customer_id;
  public $description;
  public $detail;
  public $advance_payment;
  public $by_stages;
  public $repayment_date;
  public $per_payment;
  public $hospital_location;
  public $hospital_name;
  public $counselor;
  public $project_kind;
  public $stat;
  public $url;
  public $create_date;
  public $complete_date;

  public function getSource() {
    return 'projects';
  }

  public static function getProjects($customerId) {
    $projects = Projects::find(
      array(
        'customer_id = :customerId:',
        'bind' => array('customerId' => $customerId),
        'order' => 'create_date'
      )
    );

    $projectsJson = array();
    foreach($projects as $project) {
      $projectsJson[] = $project;
    }

    return $projectsJson;
  }
}
?>

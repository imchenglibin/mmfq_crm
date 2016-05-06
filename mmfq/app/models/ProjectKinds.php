<?php
use Phalcon\Mvc\Model;
/**
 *
 */
class ProjectKinds extends Model
{
  public $id;
  public $label;

  public function getSource() {
    return 'project_kinds';
  }

  public static function addProjectKind($label) {
    $projectKind = new ProjectKinds();
    $projectKind->label = $label;
    if ($projectKind->save()) {
      return $projectKind;
    } else {
      return false;
    }
  }

  public static function deleteProjectKind($projectKindId) {
    $projectKind = ProjectKinds::findFirstById($projectKindId);
    return $projectKind && $projectKind->delete();
  }

  public static function getPorjectKinds() {
    $projectKinds = ProjectKinds::find();
    $projectKindsJson = array();
    foreach ($projectKinds as $projectKind) {
      $projectKindsJson[] = $projectKind;
    }
    return $projectKindsJson;
  }
}

?>

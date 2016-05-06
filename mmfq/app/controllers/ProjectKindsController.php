<?php
/**
 * @RoutePrefix("/api/project_kinds")
 */
class ProjectKindsController extends ControllerBase
{
  /**
   * @Route("/add_project_kind", methods={"POST"})
   */
  public function addProjectKindAction() {
    $logInUser = $this->getLogInUser();
    if (!$logInUser || $logInUser->role != 'admin') {
      return $this->returnJson(null, $this->mmfqError->forbidded);
    }

    $label = $this->request->getPost('label');

    if (strlen($label) == 0) {
      return $this->returnJson(null, $this->mmfqError->invalidParams, "(label)");
    }

    $existsKind = ProjectKinds::findFirstByLabel($label);
    if ($existsKind) {
      return $this->returnJson(null, $this->mmfqError->projectKindExists);
    }

    $projectKind = ProjectKinds::addProjectKind($label);
    if ($projectKind) {
      return $this->returnJson($projectKind);
    } else {
      return $this->returnJson(null, $this->mmfqError->serverInternalError);
    }
  }

  /**
   * @Route("/delete_project_kind", methods={"POST"})
   */
  public function deleteProjectKindAction() {
    $logInUser = $this->getLogInUser();
    if (!$logInUser || $logInUser->role != 'admin') {
      return $this->returnJson(null, $this->mmfqError->forbidded);
    }
    $projectKindId = $this->request->getPost('project_kind_id');
    if (!isset($projectKindId)) {
      return $this->returnJson(null, $this->mmfqError->invalidParams, "(project_kind_id)");
    }
    $existsProjectKind = ProjectKinds::findFirstById($projectKindId);
    if ($existsProjectKind) {
      if (ProjectKinds::deleteProjectKind($projectKindId)) {
        return $this->returnJson(null);
      } else {
        return $this->returnJson(null, $this->mmfqError->serverInternalError);
      }
    } else {
      return $this->returnJson(null, $this->mmfqError->projectKindNotExists);
    }
  }

  /**
   * @Route("/get_project_kinds", methods={"GET"})
   */
  public function getPorjectKindsAction() {
    $projectKinds = ProjectKinds::getPorjectKinds();
    return $this->returnJson($projectKinds);
  }
}

?>

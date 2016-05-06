<?php
/**
 * @RoutePrefix("/api/projects")
 */
class ProjectsController extends ControllerBase
{
  /**
   * @Route("/add_project", methods={"POST"})
   */
  public function addProjectAction() {
    $project = new Projects();
    $project->customer_id = $this->request->getPost('customer_id');
    $project->description = $this->request->getPost('description');
    $project->detail = trim($this->request->getPost('detail'));
    $project->advance_payment = $this->request->getPost('advance_payment');
    $project->by_stages = $this->request->getPost('by_stages');
    $project->repayment_date = $this->request->getPost('repayment_date');
    $project->per_payment = $this->request->getPost('per_payment');
    $project->hospital_location = $this->request->getPost('hospital_location');
    $project->hospital_name = $this->request->getPost('hospital_name');
    $project->project_kind = $this->request->getPost('project_kind');
    $project->stat = $this->request->getPost('stat');
    $project->counselor = $this->request->getPost('counselor');
    $project->url = $this->request->getPost('url');
    $project->create_date = date('Y-m-d H:i:s', time());

    $customer = Customers::findFirstById($project->customer_id);
    if (!$customer) {
      return $this->returnJson(null, $this->mmfqError->customerNotExists);
    }

    $logInUser = $this->getLogInUser();
    if (!$logInUser || $logInUser->id != $customer->user_id) {
      return $this->returnJson(null, $this->mmfqError->forbidded);
    }

    if ($project->stat != 'complete' && $project->stat != 'cancel' && $project->stat != 'want') {
      return $this->returnJson(null, $this->mmfqError->invalidParams, '(stat)');
    }

    if ($project->stat == 'complete') {
      $project->complete_date = date('Y-m-d H:i:s', time());
    }

    if (!ProjectKinds::findFirstByLabel($project->project_kind)) {
      return $this->returnJson(null, $this->mmfqError->invalidParams, '(project_kind)');
    }

    if (!isset($project->repayment_date)) {
      $project->repayment_date = date('Y-m-d H:i:s', time());
    } else {
      $project->repayment_date = date('Y-m-d H:i:s', $project->repayment_date);
    }

    if ($project->save()) {
      return $this->returnJson($project);
    } else {
      return $this->returnJson(null, $this->mmfqError->serverInternalError);
    }
  }

  /**
   * @Route("/get_projects", methods={"GET"})
   */
  public function getProjectsAction() {
    $customerId = $this->request->getQuery('customer_id');
    $customer = Customers::findFirstById($customerId);
    $logInUser = $this->getLogInUser();
    if (!$customer) {
      return $this->returnJson(null, $this->mmfqError->customerNotExists);
    } else {
      if ($logInUser->id != $customer->user_id) {
        return $this->returnJson(null, $this->mmfqError->forbidded);
      } else {
        return $this->returnJson(Projects::getProjects($customerId));
      }
    }
  }

  /**
   * @Route("/update_project_info", methods={"POST"})
   */
   public function updatePorjectAction() {
     $project_id = $this->request->getPost('project_id');
     $description = $this->request->getPost('description');
     $detail = $this->request->getPost('detail');
     $advance_payment = $this->request->getPost('advance_payment');
     $by_stages = $this->request->getPost('by_stages');
     $repayment_date = $this->request->getPost('repayment_date');
     $per_payment = $this->request->getPost('per_payment');
     $hospital_location = $this->request->getPost('hospital_location');
     $hospital_name = $this->request->getPost('hospital_name');
     $project_kind = $this->request->getPost('project_kind');
     $stat = $this->request->getPost('stat');
     $counselor = $this->request->getPost('counselor');
     $url = $this->request->getPost('url');

     $project = Projects::findFirstById($project_id);
     if (!$project) {
       return $this->returnJson(null, $this->mmfqError->projectNotExists);
     }

     $customer = Customers::findFirstById($project->customer_id);
     if (!$customer) {
       return $this->returnJson(null, $this->mmfqError->customerNotExists);
     }

     $logInUser = $this->getLogInUser();
     if (!$logInUser || $logInUser->id != $customer->user_id) {
       return $this->returnJson(null, $this->mmfqError->forbidded);
     }

     if (isset($project_kind)) {
       if (!ProjectKinds::findFirstByLabel($project_kind)) {
         return $this->returnJson(null, $this->mmfqError->invalidParams, '(project_kind)');
       }
       $project->project_kind = $project_kind;
     }

     if (isset($stat)) {
       if ($stat != 'complete' && $stat != 'cancel' && $stat != 'want') {
         return $this->returnJson(null, $this->mmfqError->invalidParams, '(stat)');
       }
       $project->stat = $stat;

       if ($stat == 'complete') {
         $project->stat = date('Y-m-d H:i:s', time());
       }
     }

     if (isset($description)) {
       $project->description = $description;
     }

     if (isset($detail)) {
       $project->detail = $detail;
     }

     if (isset($advance_payment)) {
       $project->advance_payment = $advance_payment;
     }

     if (isset($by_stages)) {
       $project->by_stages = $by_stages;
     }

     if (isset($repayment_date)) {
       $project->repayment_date = date('Y-m-d H:i:s', $repayment_date);
     }

     if (isset($per_payment)) {
       $project->per_payment = $per_payment;
     }

     if (isset($hospital_location)) {
       $project->hospital_location = $hospital_location;
     }

     if (isset($hospital_name)) {
       $project->hospital_name = $hospital_name;
     }

     if (isset($counselor)) {
       $project->counselor = $counselor;
     }

     if (isset($url)) {
       $project->url = $url;
     }

     if ($project->save()) {
       return $this->returnJson($project);
     } else {
       return $this->returnJson(null, $this->mmfqError->serverInternalError);
     }
   }

   /**
    * @Route("/delete_project", methods={"POST"})
    */
   public function deleteProjectAction() {
      $project_id = $this->request->getPost('project_id');
      $project = Projects::findFirstById($project_id);
      if (!$project) {
        return $this->returnJson(null, $this->mmfqError->projectNotExists);
      }

      $customer = Customers::findFirstById($project->customer_id);
      if (!$customer) {
        return $this->returnJson(null, $this->mmfqError->customerNotExists);
      }

      $logInUser = $this->getLogInUser();
      if (!$logInUser || $logInUser->id != $customer->user_id) {
        return $this->returnJson(null, $this->mmfqError->forbidded);
      }

      if ($project->remove()) {
        return $this->returnJson(null);
      } else {
        return $this->returnJson(null, $this->mmfqError->serverInternalError);
      }
   }
}

?>

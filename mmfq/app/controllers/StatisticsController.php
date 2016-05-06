<?php
/**
 * @RoutePrefix("/api/statistics")
 */
class StatisticsController extends ControllerBase
{
  /**
   * @Route("/get_statistics", methods={"GET"})
   */
  public function getStatisticsAction() {
    $userId = $this->request->getQuery('user_id');
    $startTime = $this->request->getQuery('start_time');
    $endTime = $this->request->getQuery('end_time');
    $stat = 'complete';

    if (!isset($userId)) {
      return $this->returnJson(null, $this->mmfqError->invalidParams, '(user_id)');
    }

    if (!isset($startTime)) {
      return $this->returnJson(null, $this->mmfqError->invalidParams, '(start_time)');
    }

    if (!isset($endTime)) {
      return $this->returnJson(null, $this->mmfqError->invalidParams, '(end_time)');
    }

    $loginUser = $this->getLogInUser();
    if (!$loginUser || $loginUser->role != 'admin') {
      return $this->returnJson(null, $this->mmfqError->forbidded);
    }

    if ($userId != -1) {
      $user = Users::findFirstById($userId);
      if (!$user) {
        return $this->returnJson(null, $this->mmfqError->userNotExists);
      }
    }

    $startTime = date('Y-m-d H:i:s', $startTime);
    $endTime = date('Y-m-d H:i:s', $endTime);
    $projects = array();
    if ($userId == -1) {
      $projects = Projects::find(
        array(
          'stat = :stat: and complete_date >= :startTime: and complete_date < :endTime:',
          'bind' => array('startTime' => $startTime, 'endTime' => $endTime, 'stat' => 'complete')
        )
      );
    } else {
      $hql = 'SELECT Projects.* FROM Customers, Projects WHERE Customers.user_id=:userId: AND Projects.customer_id=Customers.id AND Projects.stat=:stat: AND Projects.complete_date>=:startTime: AND Projects.complete_date<:endTime:';
      $projects = $this->modelsManager->executeQuery($hql, array('userId'=>$userId, 'stat'=>'complete', 'startTime'=>$startTime, 'endTime'=>$endTime));
    }

    $result = array();

    foreach ($projects as $project) {
      if (!isset($result[$project->project_kind])) {
        $result[$project->project_kind] = 1;
      } else {
        $result[$project->project_kind] = $result[$project->project_kind] + 1;
      }
    }

    $resultJson = array();
    foreach ($result as $key => $value) {
      $resultJson[] = array('label' => $key, 'data' => $value);
    }

    return $this->returnJson($resultJson);
  }
}

?>

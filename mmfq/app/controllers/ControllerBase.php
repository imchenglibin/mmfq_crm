<?php
use Phalcon\Mvc\Controller;

class ControllerBase extends Controller {
  protected function returnJson($responseObject, $error = null, $extErrorMessage = "") {
    $this->response->setHeader("Content-Type", "application/json");
    if ($error == null) {
      $error = $this->mmfqError->success;
    }
    $returnObject = array(
      'data' => $responseObject,
      'code' => $error->code,
      'message' => $error->message . $extErrorMessage
    );
    $jsonObject = json_encode($returnObject, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
    $this->response->setContent($jsonObject);
    return $this->response;
  }

  protected function getLogInUser() {
    $userId = $this->session->get('user_id');
    return Users::findFirstById($userId);
  }

  protected function saveLogInUser($user) {
    if ($user) {
      $this->session->set('user_id', $user->id);
    } else {
      $this->session->remove('user_id');
    }
  }
}

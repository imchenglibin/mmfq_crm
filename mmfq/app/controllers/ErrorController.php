<?php
/**
 * @RoutePrefix("/api/error")
 */
class ErrorController extends ControllerBase
{
  /**
   * @Route("/forbidden_error", methods={"GET"})
   */
   public function forbiddenErrorAction() {
     return $this->returnJson(null, $this->mmfqError->forbidded);
   }

   /**
    * @Route("/authentication_error", methods={"GET"})
    */
    public function authenticationErrorAction() {
      return $this->returnJson(null, $this->mmfqError->authenticationFailed);
    }

    /**
     * @Route("/not_found_error", methods={"GET"})
     */
     public function notFoundErrorAction() {
       return $this->returnJson(null, $this->mmfqError->notFound);
     }
}

?>

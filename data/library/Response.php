<?php

require_once('Zend/Json.php');

class Response {
  public $success, $data, $message;

  public function __construct($params = array()) {
    $this->success  = isset($params["success"]) ? $params["success"] : false;
    $this->message  = isset($params["message"]) ? $params["message"] : '';
    $this->data     = isset($params["data"])    ? $params["data"]    : array();
  }

  public function to_json() {
    return Zend_Json::encode(array(
        'success'   => isset($this->success) ? $this->success : false,
        'message'   => isset($this->message) ? $this->message : '',
        'data'      => isset($this->data) ? $this->data : array()
    ));
  }

  public function send() {
    //clear any previous output
    ob_clean();
    //send no cacheing (date in past)
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Sat, 26 Jul 1997 00:00:00 GMT");
    //contenttype json
    header('Content-type: application/json');
    print_r($this->to_json());
    flush();
  }
}
?>

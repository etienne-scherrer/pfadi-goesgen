<?php

require_once('../library/AbstractExtJSHandler.php');

class TextsHandler extends AbstractExtJSHandler {

  protected function getConfiguredDateColumns() {
    return array('evt_create' => 'evt_create');
  }

  protected function getConfiguredTableName() {
    return 'am_text';
  }

  protected function getConfiguredIdColumn() {
    return 'text_nr';
  }

  protected function execUpdateDataBeforeCreate($data) {
    //set creation user name
    $data['user_nr']=$_SESSION['user_nr'];
    return $data;
  }
}

$handler = new TextsHandler();
$handler->handle();

?>
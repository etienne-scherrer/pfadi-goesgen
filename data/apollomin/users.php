<?php

require_once('../library/AbstractExtJSHandler.php');

class UserCodesHandler extends AbstractExtJSHandler {
  protected function getConfiguredDateColumns() {
    return array('evt_create' => 'evt_create');
  }

  protected function getConfiguredTableName() {
    return 'am_user';
  }

  protected function getConfiguredIdColumn() {
    return 'user_nr';
  }
}

$handler = new UserCodesHandler();
$handler->handle();

?>
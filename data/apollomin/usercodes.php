<?php

require_once('../library/AbstractExtJSHandler.php');

class UserCodesHandler extends AbstractExtJSHandler {
  protected function getConfiguredTableName() {
    return 'am_uc';
  }

  public function handleCreateRequest($request) {
    $this->returnFailed("Not allowed");
  }

  public function handleUpdateRequest($request, $n = 0) {
    $this->returnFailed("Not allowed");
  }

  public function handleDeleteRequest($request) {
    $this->returnFailed("Not allowed");
  }
}

$handler = new UserCodesHandler();
$handler->handle();

?>
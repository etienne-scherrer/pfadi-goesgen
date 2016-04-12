<?php

require_once('../library/AbstractHandler.php');

class LocalEventsHandler extends AbstractHandler {

  protected function getDateColumns() {
    return array('evt_create' => 'evt_create','evt_start' => 'evt_start','evt_end' => 'evt_end');
  }

  protected function execHandle(){
    $stmt = $this->db->query(
        'SELECT * FROM am_text AS t JOIN am_event AS e ON t.text_nr = e.text_nr WHERE t.type_uid=? AND DATE_ADD(e.evt_end,INTERVAL 1 DAY) >= NOW() ORDER BY e.evt_start LIMIT 1',
        array($_GET["typeUid"])
    );

    $fetcher = new Zend_Db_Statement_Mysqli_Datemodifier($stmt, $this->getDateColumns());
    $result = $fetcher->fetchAllWithDateModified();
    $this->returnSuccess($result);
  }
}

$handler = new LocalEventsHandler();
$handler->handle();
?>
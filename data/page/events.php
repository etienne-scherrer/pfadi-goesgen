<?php

require_once('../library/AbstractHandler.php');

class LocalEventsHandler extends AbstractHandler
{

    protected function getDateColumns()
    {
        return ['evt_create' => 'evt_create', 'evt_start' => 'evt_start', 'evt_end' => 'evt_end'];
    }

    protected function execHandle()
    {
        $select = $this->db->select()
            ->from(['t' => 'am_text'])
            ->join(['e' => 'am_event'], 't.text_nr = e.text_nr')
            ->where('t.type_uid = ?', !empty($_GET['typeUid']) ? $_GET['typeUid'] : null)
            ->where('DATE_ADD(e.evt_end, INTERVAL 1 DAY) >= NOW()')
            ->where('t.deleted = ?', 0)
            ->where('e.deleted = ?', 0)
            ->order('e.evt_start')
            ->limit(1);
        $stmt   = $select->query();

        $fetcher = new Zend_Db_Statement_Mysqli_Datemodifier($stmt, $this->getDateColumns());
        $result  = $fetcher->fetchAllWithDateModified();
        $this->returnSuccess($result);
    }
}

$handler = new LocalEventsHandler();
$handler->handle();
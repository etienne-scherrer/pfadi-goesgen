<?php

require_once('../library/AbstractHandler.php');

class LocalTextsHandler extends AbstractHandler
{

    protected function getDateColumns()
    {
        return ['evt_create' => 'evt_create'];
    }

    protected function execHandle()
    {
        $select = $this->db->select()
            ->from(['t' => 'am_text'])
            ->where('t.type_uid = ?', !empty($_GET['typeUid']) ? $_GET['typeUid'] : null)
            ->where('t.deleted = ?', 0)
            ->order('t.evt_create DESC');
        $stmt   = $select->query();

        $fetcher = new Zend_Db_Statement_Mysqli_Datemodifier($stmt, $this->getDateColumns());
        $result  = $fetcher->fetchAllWithDateModified();
        $this->returnSuccess($result);
    }
}

$handler = new LocalTextsHandler();
$handler->handle();
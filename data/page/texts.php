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
        $stmt = $this->db->query(
            'SELECT * FROM am_text AS t WHERE t.type_uid=? ORDER BY t.evt_create DESC',
            [!empty($_GET["typeUid"]) ? $_GET["typeUid"] : null]
        );

        $fetcher = new Zend_Db_Statement_Mysqli_Datemodifier($stmt, $this->getDateColumns());
        $result  = $fetcher->fetchAllWithDateModified();
        $this->returnSuccess($result);
    }
}

$handler = new LocalTextsHandler();
$handler->handle();
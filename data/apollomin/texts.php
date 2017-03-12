<?php

require_once('../library/AbstractExtJSHandler.php');

class TextsHandler extends AbstractExtJSHandler
{
    /**
     * @return array
     */
    protected function getConfiguredDateColumns()
    {
        return ['evt_create' => 'evt_create'];
    }

    /**
     * @return string
     */
    protected function getConfiguredTableName()
    {
        return 'am_text';
    }

    /**
     * @return string
     */
    protected function getConfiguredIdColumn()
    {
        return 'text_nr';
    }

    /**
     * @param array $data
     * @return array
     */
    protected function execUpdateDataBeforeCreate($data)
    {
        //set creation user name
        $data['user_nr'] = $_SESSION['user_nr'];
        return $data;
    }

    /**
     * @param Zend_Db_Select $select
     */
    protected function createReadFrom($select)
    {
        $select->from(['t' => 'am_text'])->where('t.deleted = ?', 0);
    }
}

$handler = new TextsHandler();
$handler->handle();
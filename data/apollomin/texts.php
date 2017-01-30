<?php


require_once('../library/AbstractExtJSHandler.php');


class TextsHandler extends AbstractExtJSHandler
{
    protected function getConfiguredDateColumns()
    {
        return ['evt_create' => 'evt_create'];
    }

    protected function getConfiguredTableName()
    {
        return 'am_text';
    }

    protected function getConfiguredIdColumn()
    {
        return 'text_nr';
    }

    protected function execUpdateDataBeforeCreate($data)
    {
        //set creation user name
        $data['user_nr'] = $_SESSION['user_nr'];
        return $data;
    }

    protected function createReadFrom($select)
    {
        $select->from(['t' => 'am_text'])->where('t.deleted = ?', 0);
    }
}

$handler = new TextsHandler();
$handler->handle();
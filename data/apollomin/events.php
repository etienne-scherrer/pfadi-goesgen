<?php

require_once('../library/AbstractExtJSHandler.php');

class EventsHandler extends AbstractExtJSHandler
{
    //static database metadata
    private $textDbTableColumns;
    private $eventDbTableColumns;

    function __construct()
    {
        parent::__construct();

        $this->textDbTableColumns = [
            'text_nr'         => 'text_nr',
            'user_nr'         => 'user_nr',
            'type_uid'        => 'type_uid',
            'title'           => 'title',
            'teaser'          => 'teaser',
            'text'            => 'text',
            'additional_text' => 'additional_text',
            'evt_create'      => 'evt_create',
            'image_path'      => 'image_path'];

        $this->eventDbTableColumns = [
            'event_nr'       => 'event_nr',
            'text_nr'        => 'text_nr',
            'start_location' => 'start_location',
            'end_location'   => 'end_location',
            'evt_start'      => 'evt_start',
            'evt_end'        => 'evt_end'];
    }


    protected function getConfiguredIdColumn()
    {
        return 'event_nr';
    }

    protected function getConfiguredDateColumns()
    {
        return ['evt_create' => 'evt_create', 'evt_start' => 'evt_start', 'evt_end' => 'evt_end'];
    }

    protected function getConfiguredTableName()
    {
        return 'am_event';
    }

    protected function execUpdateDataBeforeCreate($data)
    {
        //filter data
        return array_intersect_key($data, $this->eventDbTableColumns);
    }

    protected function execUpdateDataBeforeUpdate($data)
    {
        //filter data
        return array_intersect_key($data, $this->eventDbTableColumns);
    }

    public function handleCreateRequest($request)
    {
        $db = $this->db;
        //create texts part
        //create new text id and set it
        $request->data['text_nr'] = $db->fetchOne('SELECT am_nextval()');
        //insert text data
        $db->insert('am_text', array_intersect_key($request->data, $this->textDbTableColumns));

        //create events part
        parent::handleCreateRequest($request);
    }

    public function handleUpdateRequest($request, $n = 0)
    {
        //update texts part
        $id = $request->data['text_nr'];
        $n  = $n + $this->db->update('am_text', array_intersect_key($request->data, $this->textDbTableColumns), ['text_nr = ?' => $id]);
        //update events part
        parent::handleUpdateRequest($request, $n);
    }

    public function handleDeleteRequest($request)
    {
        //delete texts part
        $id = $request->data[$this->getConfiguredIdColumn()];
        $n  = $this->db->delete('am_text', ['text_nr = (SELECT text_nr FROM am_event WHERE event_nr = ?)' => $id]);
        if ($n == 0) {
            $this->returnFailed("No records were deleted. Record with id " . $id . " in am_event not found.");
        } else {
            //delete events part
            parent::handleDeleteRequest($request);
        }
    }

    protected function createReadFrom($select)
    {
        $select->from(['e' => 'am_event'])->join(['t' => 'am_text'], 'e.text_nr = t.text_nr');
    }
}

$handler = new EventsHandler();
$handler->handle();

?>
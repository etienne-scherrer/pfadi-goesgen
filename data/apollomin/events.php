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

    /**
     * @return string
     */
    protected function getConfiguredIdColumn()
    {
        return 'event_nr';
    }

    /**
     * @return array
     */
    protected function getConfiguredDateColumns()
    {
        return ['evt_create' => 'evt_create', 'evt_start' => 'evt_start', 'evt_end' => 'evt_end'];
    }

    /**
     * @return string
     */
    protected function getConfiguredTableName()
    {
        return 'am_event';
    }

    /**
     * @param array $data
     * @return array
     */
    protected function execUpdateDataBeforeCreate($data)
    {
        //filter data
        return array_intersect_key($data, $this->eventDbTableColumns);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function execUpdateDataBeforeUpdate($data)
    {
        //filter data
        return array_intersect_key($data, $this->eventDbTableColumns);
    }

    /**
     * @param Request $request
     */
    public function handleCreateRequest($request)
    {
        $db                       = $this->db;
        $request->data['user_nr'] = $_SESSION['user_nr'];
        unset($request->data['text_nr'], $request->data['event_nr']);

        //insert text data
        $db->insert('am_text', array_intersect_key($request->data, $this->textDbTableColumns));
        $request->data['text_nr'] = $db->lastInsertId('am_text');

        //create events part
        parent::handleCreateRequest($request);
    }

    /**
     * @param Request $request
     * @param int     $n
     */
    public function handleUpdateRequest($request, $n = 0)
    {
        //update texts part
        $request->data['user_nr'] = $_SESSION['user_nr'];
        $n                        = $n + $this->db->update('am_text', array_intersect_key($request->data, $this->textDbTableColumns), ['text_nr = ?' => $request->data['text_nr']]);
        //update events part
        parent::handleUpdateRequest($request, $n);
    }

    /**
     * @param Request $request
     */
    public function handleDeleteRequest($request)
    {
        //delete texts part
        $id = $request->data[$this->getConfiguredIdColumn()];
        $n  = $this->db->update('am_text', ['deleted' => 1], ['text_nr = (SELECT text_nr FROM am_event WHERE event_nr = ?)' => $id]);
        if ($n == 0) {
            $this->returnFailed("No records were deleted. Record with id " . $id . " in am_event not found.");
        } else {
            //delete events part
            parent::handleDeleteRequest($request);
        }
    }

    /**
     * @param Zend_Db_Select $select
     */
    protected function createReadFrom($select)
    {
        $select->from(['e' => 'am_event'])->join(['t' => 'am_text'], 'e.text_nr = t.text_nr')->where('e.deleted = ?', 0);
    }
}

$handler = new EventsHandler();
$handler->handle();
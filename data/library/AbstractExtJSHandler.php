<?php

require_once('AbstractHandler.php');

abstract class AbstractExtJSHandler extends AbstractHandler{

  /**
   * @return The name of the table
   */
  abstract protected function getConfiguredTableName();

  /**
   * @return The column name of the id.array
   *
   * This is needed if you use create / update / delete
   */
  protected function getConfiguredIdColumn(){
    return null;
  }

  /**
   * @return a array with columns that are dates. The array key must be equal to the values!
   */
  protected function getConfiguredDateColumns() {
    return null;
  }

  /**
   * Hook if data sent from Ext JS needs to be modified prior storing.
   * E.g. set an EVT_CREATE timestamp
   */
  protected function execUpdateDataBeforeCreate($data) {
    return $data;
  }

  /**
   * Hook if data sent from Ext JS needs to be modified prior storing.
   * E.g. update an EVT_UPDATE timestamp
   */
  protected function execUpdateDataBeforeUpdate($data) {
    return $data;
  }

  public function handleCreateRequest($request) {
    $data = $this->execUpdateDataBeforeCreate($request->data);
    $tablename = $this->getConfiguredTableName();
    $idName = $this->getConfiguredIdColumn();
    $db = $this->db;
    //create new id and set it
    $newId = $db->fetchOne('SELECT am_nextval()');
    $data[$idName] = $newId;
    //insert data
    $db->insert($tablename, $data);
    $this->returnSuccess($this->getRecord($newId), "Record created");
  }

  public function handleUpdateRequest($request, $n = 0) {
    $data = $this->execUpdateDataBeforeUpdate($request->data);
    $tablename = $this->getConfiguredTableName();
    $idName = $this->getConfiguredIdColumn();
    $id = $data[$idName];
    $n = $n + $this->db->update($tablename, $data, array($idName . ' = ?' => $id));
    if($n == 0){
      $this->returnFailed("No records were updated. Record with id " . $id . " in " . $tablename . " not found.");
    }
    else {
      $this->returnSuccess($this->getRecord($id), $n . " records updated");
    }
  }

  public function handleDeleteRequest($request) {
    $tablename = $this->getConfiguredTableName();
    $idName = $this->getConfiguredIdColumn();
    $id = $request->data[$idName];
    $n = $this->db->delete($tablename, array($idName . ' = ?' => $id));
    if($n == 0){
      $this->returnFailed("No records were deleted. Record with id " . $id . " in " . $tablename . " not found.");
    }
    else {
      $this->returnSuccess(null, $n . " records deleted");
    }
  }

  protected function handleReadRequest(){
    //create select statement
    $select = $this->db->select();

    $this->createReadFrom($select);

    $this->createReadLimit($select);

    $this->createReadWhere($select);

    $this->createReadSort($select);

    $stmt = $select->query();

    $fetcher = new Zend_Db_Statement_Mysqli_Datemodifier($stmt, $this->getConfiguredDateColumns());
    $result = $fetcher->fetchAllWithDateModified();

    $this->logger->info("Query String: " . $select->__toString());
    $this->returnSuccess($result, "Data loaded");
  }

  protected function createReadFrom($select) {
    $select->from($this->getConfiguredTableName());
  }

  protected function createReadLimit($select) {
    //FIXME rst does not work properly with ext js paging...
    //if(array_key_exists('limit', $_GET) && array_key_exists('start', $_GET)){
      //$select->limit($_GET['limit'], $_GET['start']);
    //}
  }

  protected function createReadWhere($select){
    if(!isset($_GET['filter'])){
      return;
    }
    $filterJson = Zend_Json::decode($_GET['filter']);
    if(empty($filterJson) || !is_array($filterJson)){
      return;
    }
    foreach ($filterJson as $filter) {
      $select->where($filter['property'] . " = ?", $filter['value']);
    }
  }

  protected function createReadSort($select) {
    if(!isset($_GET['sort'])){
      return;
    }
    $sortJson = Zend_Json::decode($_GET['sort']);
    if(empty($sortJson) || !is_array($sortJson)){
      return;
    }
    foreach ($sortJson as $sort) {
      $select->order($sort['property'] . " " . $sort['direction']);
    }
  }

  public function getRecord($id) {
    $select = $this->db->select();
    $this->createReadFrom($select);
    $select->where($this->getConfiguredIdColumn() . " = ?", $id);

    $stmt = $select->query();

    $fetcher = new Zend_Db_Statement_Mysqli_Datemodifier($stmt, $this->getConfiguredDateColumns());
    $result = $fetcher->fetchAllWithDateModified();

    return $result[0];
  }

  protected function execHandle(){
    parent::execHandle();
    switch ($_SERVER["REQUEST_METHOD"]) {
      case "GET":{
        $this->handleReadRequest();
        break;
      }
      case "PUT":{
        $this->handleCreateRequest(new Request());
        break;
      }
      case "POST":{
        $this->handleUpdateRequest(new Request());
        break;
      }
      case "DELETE":{
        $this->handleDeleteRequest(new Request());
        break;
      }
    }
  }
}
?>
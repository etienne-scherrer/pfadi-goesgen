<?php

require_once('Zend/Db.php');
require_once('Zend/Db/Statement/Mysqli.php');
require_once('Zend/Date.php');

/**
 * We exend from the Zend_Db_Statement_Mysqli in order to get access to the
 * protected vars in Zend_Db_Statement_Mysqli
 * @author rst
 */
class Zend_Db_Statement_Mysqli_Datemodifier extends Zend_Db_Statement_Mysqli
{
  private $originalstmt;
  private $dateColumns;

  public function __construct($stmt, $dateColumns)
  {
    $this->originalstmt = $stmt;

    if (!empty($dateColumns) && is_array($dateColumns)) {
      $this->dateColumns = $dateColumns;
    } else {
      $this->dateColumns = [];
    }
  }

  function fetchAllWithDateModified()
  {
    $data = [];
    while ($row = $this->fetchWithDateModified()) {
      $data[] = $row;
    }
    return $data;
  }

  function fetchWithDateModified()
  {
    if (!$this->originalstmt->_stmt) {
      return false;
    }
    // fetch the next result
    $retval = $this->originalstmt->_stmt->fetch();
    switch ($retval) {
      case null: // end of data
      case false: // error occurred
        $this->originalstmt->_stmt->reset();
        return false;
      default:
        // fallthrough
    }
    $row = [];
    foreach ($this->originalstmt->_values as $index => $val) {
      $key = $this->originalstmt->_keys[$index];
      if (array_key_exists($key, $this->dateColumns) && $val) {
        //reformat date:
        $date      = new Zend_Date($val, Zend_Date::ISO_8601);
        $row[$key] = $date->get(Zend_Date::ISO_8601);
      } else {
        $row[$key] = $val;
      }
    }
    return $row;
  }
}

?>
<?php

require_once('../library/AbstractExtJSHandler.php');

class UserHandler extends AbstractExtJSHandler
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
        return 'am_user';
    }

    /**
     * @return string
     */
    protected function getConfiguredIdColumn()
    {
        return 'user_nr';
    }

    protected function handleReadRequest()
    {
        //create select statement
        $select = $this->db->select();

        $this->createReadFrom($select);

        $this->createReadLimit($select);

        $this->createReadWhere($select);

        $this->createReadSort($select);

        $userConfig = (new Zend_Config_Ini('../config.ini', 'user'))->toArray();
        if (!in_array($_SESSION['user_nr'], $userConfig['user']['admin']['id'])) {
            $select->where($this->getConfiguredIdColumn() . ' = ?', $_SESSION['user_nr']);
        }

        $stmt = $select->query();

        $fetcher = new Zend_Db_Statement_Mysqli_Datemodifier($stmt, $this->getConfiguredDateColumns());
        $result  = $fetcher->fetchAllWithDateModified();

        $this->logger->info("Query String: " . $select->__toString());
        $this->returnSuccess($result, "Data loaded");
    }

    /**
     * @param array $data
     * @return array
     */
    protected function execUpdateDataBeforeCreate($data)
    {
        return $this->hashPassword($data);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function execUpdateDataBeforeUpdate($data)
    {
        return $this->hashPassword($data);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function hashPassword($data)
    {
        if (isset($data['password'])) {
            $data['password']       = password_hash($data['password'], PASSWORD_DEFAULT);
            $data['passwordHashed'] = 1;
        }

        return $data;
    }
}

$handler = new UserHandler();
$handler->handle();
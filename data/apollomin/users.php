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
        if(isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $data['passwordHashed'] = 1;
        }

        return $data;
    }
}

$handler = new UserHandler();
$handler->handle();
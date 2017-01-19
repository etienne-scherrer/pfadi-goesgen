<?php

require_once('../library/AbstractHandler.php');

class AuthenticationHandler extends AbstractHandler
{
    
    function createNewSessionWithChallenge()
    {
        session_unset();
        srand();
        $challenge = "";
        for ($i = 0; $i < 80; $i++) {
            $challenge .= dechex(rand(0, 15));
        }
        $_SESSION['challenge'] = $challenge;
        echo $challenge;
        exit();
    }

    /**
     * @param $username
     * @return string
     */
    function getPasswordForUser($username)
    {
        return $this->db->fetchOne('SELECT password FROM am_user WHERE login = ?', [$username]);
    }

    /**
     * @param $username
     * @return string
     */
    function getUserNr($username)
    {
        return $this->db->fetchOne('SELECT user_nr FROM am_user WHERE login = ?', [$username]);
    }

    /**
     * @param $username
     * @return bool
     */
    function isPasswordHashed($username)
    {
        return (bool)$this->db->fetchOne('SELECT passwordHashed FROM am_user WHERE login = ?', [$username]);
    }

    /**
     * @param $challenge
     * @param $response
     * @param $password
     * @return bool
     */
    function validate($challenge, $response, $password)
    {
        return password_verify($response, $password) || md5($challenge . $password) === md5($challenge . $response);
    }

    /**
     * @param $md5Password
     * @param $username
     * @throws Zend_Db_Adapter_Exception
     */
    function updatePasswordHash($md5Password, $username)
    {
        $this->db->update('am_user', ['password' => password_hash($md5Password, PASSWORD_DEFAULT), 'passwordHashed' => 1], ['login = ?' => $username]);
    }

    /**
     * @param $challenge
     * @param $response
     * @param $username
     * @throws Exception
     */
    function authenticate($challenge, $response, $username)
    {
        $password = $this->getPasswordForUser($username);
        if ($this->validate($challenge, $response, $password)) {
            if (!$this->isPasswordHashed($username)) {
                $this->updatePasswordHash($response, $username);
            }
            $_SESSION['authenticated'] = "yes";
            $_SESSION['username']      = $username;
            $_SESSION['user_nr']       = $this->getUserNr($username);
            unset($_SESSION['challenge']);
            $this->returnSuccess(null);
        } else {
            throw new Exception("Falscher Benutzername oder Passwort");
        }
    }

    /**
     * @throws Exception
     */
    protected function execHandle()
    {
        session_start();
        switch ($_SERVER["REQUEST_METHOD"]) {
            case "GET": {
                $this->createNewSessionWithChallenge();
                break;
            }
            case "POST": {
                if (isset($_SESSION['challenge']) && isset($_REQUEST['response']) && isset($_REQUEST['username'])) {
                    $this->authenticate($_SESSION['challenge'], $_REQUEST['response'], $_REQUEST['username']);
                } else {
                    throw new Exception("Session ist abgelaufen");
                }
                break;
            }
        }
    }
}

$handler = new AuthenticationHandler();
$handler->handle();
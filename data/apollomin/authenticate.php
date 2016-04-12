<?php 

require_once('../library/AbstractHandler.php');

class AuthenticationHandler extends AbstractHandler{

  function createNewSessionWithChallenge() {
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

  function getPasswordForUser($username) {
    return $this->db->fetchOne('SELECT password FROM am_user WHERE login=?', array($username));
  }

  function getUserNr($username) {
    return $this->db->fetchOne('SELECT user_nr FROM am_user WHERE login=?', array($username));
  }

  function validate($challenge, $response, $password) {
    return md5($challenge . $password) == $response;
  }

  function authenticate($challenge, $response, $username) {
    $password = $this->getPasswordForUser($username);
    if ($this->validate($challenge, $response, $password)) {
      $_SESSION['authenticated'] = "yes";
      $_SESSION['username'] = $username;
      $_SESSION['user_nr'] = $this->getUserNr($username);
      unset($_SESSION['challenge']);
      $this->returnSuccess(null);
    } else {
      throw new Exception("Falscher Benutzername oder Passwort");
    }
  }

  protected function execHandle(){
    session_start();
    switch ($_SERVER["REQUEST_METHOD"]) {
      case "GET":{
        $this->createNewSessionWithChallenge();
        break;
      }
      case "POST":{
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
?>
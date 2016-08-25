<?php

require_once('Zend/Config.php');
require_once('Zend/Config/Ini.php');
require_once('Zend/Db.php');
require_once('Zend/Db/Statement/Mysqli.php');
require_once('Zend/Json.php');
require_once('Zend/Log.php');
require_once('Zend/Log/Writer/Db.php');
require_once('Zend/Log/Filter/Message.php');
require_once('Zend/Date.php');
require_once('Request.php');
require_once('Response.php');
require_once('Zend_Db_Statement_Mysqli_Datemodifier.php');

// Zend_Loader::registerAutoload();

abstract class AbstractHandler
{

    protected $db;//Zend_Db_Adapter_Abstract

    protected $logger;

    function __construct()
    {
        //Ensure timezone is set. This should be done in php.ini
        date_default_timezone_set('Europe/Berlin');

        $config = new Zend_Config_Ini('../../data/config.ini', 'db');

        $this->db = Zend_Db::factory($config->database);

        $columnMapping = ['priority' => 'priorityName', 'timestamp' => 'timestamp', 'message' => 'message'];
        $writer        = new Zend_Log_Writer_Db($this->db, 'am_log', $columnMapping);
        //$writer = new Zend_Log_Writer_Stream('testlog.txt');
        $this->logger = new Zend_Log($writer);
        //$filter = new Zend_Log_Filter_Priority(Zend_Log::CRIT); //only log critical message
        //$filter = new Zend_Log_Filter_Message("/date/"); //ignore date warnings
        //$this->logger->addFilter($filter);
        ini_set("display_errors", 0);
        $this->logger->registerErrorHandler();
    }

    public function isAuthenticated()
    {
        return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] == "yes";
    }

    public function checkAccess()
    {
        //send unauthorized header if not authenticated
        if (!$this->isAuthenticated()) {
            header('HTTP/1.1 401 Unauthorized');
            exit();
        }
    }

    /**
     * Useful for debugging
     * @param mixin $var
     */
    public function logVar($var)
    {
        $this->logger->info(print_r($var, true));
    }

    protected function returnFailed($message = null)
    {
        $res          = new Response();
        $res->success = false;
        $res->message = $message;
        $res->send();
        exit();
    }

    protected function returnSuccess($data, $message = null)
    {
        $res          = new Response();
        $res->success = true;
        $res->data    = $data;
        $res->message = $message;
        $res->send();
        exit();
    }

    protected function execHandle()
    {
        session_start();
        $this->checkAccess();
    }

    public function handle()
    {
        try {
            $this->execHandle();
        } catch (Exception $e) {
            $this->returnFailed($e->getMessage());
        }
    }
}

?>
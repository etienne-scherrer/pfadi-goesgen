<?php


require_once('Zend/Json.php');

class Request
{
    public $data;

    public function __construct()
    {
        $this->parseRequest();
    }

    protected function parseRequest()
    {
        if (isset($_REQUEST['data'])) {
            $this->data = Zend_Json::decode($_REQUEST['data']);
        } else {
            $raw         = '';
            $httpContent = fopen('php://input', 'r');
            while ($kb = fread($httpContent, 1024)) {
                $raw .= $kb;
            }
            $params = Zend_Json::decode($raw);
            if ($params) {
                $this->data = $params['data'];;
            }
        }
    }
}

?>

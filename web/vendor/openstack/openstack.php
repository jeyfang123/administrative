<?php
class OpenStack
{
    protected $_req = null;
    function __construct($ip, $port, $token = ''){
        $this->_req = new HttpRequest($ip, $port);

        $this->setToken($token);
    }

    function setToken($token){
        if($token != ''){
            $this->_req->header('X-Auth-Token', $token);
        }
    }
}
?>
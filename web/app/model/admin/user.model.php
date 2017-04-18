<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/17
 * Time: 17:34
 */

class UserModel{
    private $_db;
    function __construct()
    {
        $this->_db = DB::getInstance();
    }

    function login($username,$password){
        $sql = "select * from ".DB::TB_BG_USER." where username = ? and pwd = ? limit 1";
        return $this->_db->getRow($sql,[$username,$password]);
    }
}
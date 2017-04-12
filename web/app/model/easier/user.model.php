<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/12
 * Time: 11:12
 */
class UserModel{
    private $_db;

    public function __construct() {
        $this->_db = DB::getInstance ();
    }

    public function getUser($username,$password){
        $sql = "select count(*) as count from ".DB::TB_USER." where username = ? and pwd = ? ";
        return $this->_db->getOne($sql,[$username,$password]);
    }
}
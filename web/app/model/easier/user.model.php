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
        $sql = "select * from ".DB::TB_USER." where username = ? and pwd = ? ";
        return $this->_db->GetAll($sql,[$username,$password]);
    }

    public function checkUsernameUnique($username){
        $sql = "select count(*) from ".DB::TB_USER." where username = ? ";
        return $this->_db->GetOne($sql,[$username]);
    }

    public function addUser($username,$password){
        $sql = "insert into ".DB::TB_USER."(username,pwd) values(?,?)";
        return $this->_db->Execute($sql,[$username,$password]);
    }
}
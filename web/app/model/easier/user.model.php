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
        $time = date('Y-m-d H:i:s');
        $sql = "insert into ".DB::TB_USER."(username,pwd,createtime) values(?,?,?)";
        return $this->_db->Execute($sql,[$username,$password,$time]);
    }

    public function perValue($phone,$compellation,$id,$user){
        $userId = $user['id'];
        $time = date('Y-m-d H:i:s');
        $checkSql = "select valued from ".DB::TB_USER." where id = ?";
        $check = $this->_db->GetOne($checkSql,[$userId]);
        if($check != '0'){
            return 'exist';
        }
        else{
            $sql = "update ".DB::TB_USER." 
            set phone = ? ,compellation = ?,citizenid = ?,valued = ?,valuedtime = ? where id = ?";
            $res = $this->_db->Execute($sql,[$phone,$compellation,$id,1,$time,$userId]);
            return $res;
        }
    }

    public function enterValue($phone,$artificial,$enterprise,$creditcode,$user){
        $userId = $user['id'];
        $time = date('Y-m-d H:i:s');
        $checkSql = "select valued from ".DB::TB_USER." where id = ?";
        $check = $this->_db->GetOne($checkSql,[$userId]);
        if($check != '0'){
            return 'exist';
        }
        else{
            $sql = "update ".DB::TB_USER." 
            set phone = ? ,artificial = ?,enterprise = ?,creditcode = ?,valued = ?,valuedtime = ? where id = ?";
            $res = $this->_db->Execute($sql,[$phone,$artificial,$enterprise,$creditcode,2,$time,$userId]);
            return $res;
        }
    }
}
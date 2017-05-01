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
        $sql = "select * from ".DB::TB_ROLE_USER." where username = ? and password = ? limit 1";
        return $this->_db->getRow($sql,[$username,$password]);
    }

    /**
     * 获取所有部门人员
     * @return array|bool|null
     */
    function getDepartmentUser(){
        $userSql = "select * from ".DB::TB_ROLE_USER." where enable = '1' ";
        $res = $this->_db->GetAll($userSql);
        return DB::returnModelRes($res)[0];
    }

    /**
     * 添加部门人员
     * @param $username
     * @param $role
     * @return string
     */
    function addRoleuser($role,$username){
        $datetime= date('Y-m-d H:i:s');
        $existSql = "select count(*) from ".DB::TB_ROLE_USER." where username = ? ";
        $existRes = $this->_db->GetOne($existSql,[$username]);
        if($existRes > 0){
            return 'exist';
        }
        $insetSql = "insert into ".DB::TB_ROLE_USER."(role,username,password,nickname,createtime) 
                     values(?,?,'e10adc3949ba59abbe56e057f20f883e',?,'{$datetime}')";
        $res = $this->_db->Execute($insetSql,[$role,$username,$username]);
        return $res;
    }
}
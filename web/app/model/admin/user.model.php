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
    function addRoleuser($role,$username,$avatar){
        $datetime= date('Y-m-d H:i:s');
        $existSql = "select count(*) from ".DB::TB_ROLE_USER." where username = ? ";
        $existRes = $this->_db->GetOne($existSql,[$username]);
        if($existRes > 0){
            return 'exist';
        }
        $insetSql = "insert into ".DB::TB_ROLE_USER."(role,username,password,nickname,createtime,avatar) 
                     values(?,?,'e10adc3949ba59abbe56e057f20f883e',?,'{$datetime}',?)";
        $res = $this->_db->Execute($insetSql,[$role,$username,$username,$avatar]);
        return $res;
    }

    /**
     * 获取部门人员总数
     * @return mixed
     */
    function getTotalRoleUser(){
        $sql = "select count(*) from ".DB::TB_ROLE_USER." where enable = '1'";
        $count = $this->_db->GetOne($sql);
        return $count;
    }

    /**
     * 获取所有部门人员（根据关键字搜索）
     * @param $keyword
     * @param $page
     * @param $pageSize
     * @return mixed
     */
    function getRoleUser($keyword,$page,$pageSize){
        $page = ($page -1) * $pageSize;
        if(!empty($keyword)){
            $sql = "select depart_user_id,avatar,rolename,nickname,users.createtime,COALESCE(phone,email,'') as contact,
                case when phone IS NOT NULL then 'fa fa-phone'
                when email IS NOT NULL then 'fa fa-envelope'
                else '' end as tag from ". DB::TB_ROLE_USER ." users LEFT JOIN ". DB::TB_ROLE ." roles on
                role = role_id where users.enable = '1' and roles.\"createuser\" is not NULL and  nickname = ? or username = ? order by users.createtime offset {$page} limit {$pageSize} ";
            $res = $this->_db->GetAll($sql,[$keyword,$keyword]);
            $countSql = "select count(*) from ". DB::TB_ROLE_USER ." users LEFT JOIN ". DB::TB_ROLE ." roles on role = role_id where users.enable = '1' and roles.\"createuser\" is not NULL and rolename = ? ";
            $count = $this->_db->GetOne($countSql,[$keyword]);
            return DB::returnModelRes(['data'=>$res,'count'=>$count])[0];
        }
        else{
            $sql = "select depart_user_id,avatar,rolename,nickname,users.createtime,COALESCE(phone,email,'') as contact,
                case when phone IS NOT NULL then 'fa fa-phone'
                when email IS NOT NULL then 'fa fa-envelope'
                else '' end as tag from ". DB::TB_ROLE_USER ." users LEFT JOIN ". DB::TB_ROLE ." roles on
                role = role_id where users.enable = '1' and roles.\"createuser\" is not NULL order by users.createtime offset {$page} limit {$pageSize}  ";
            $res = $this->_db->GetAll($sql);
            $countSql = "select count(*) from ". DB::TB_ROLE_USER ." users LEFT JOIN ". DB::TB_ROLE ." roles on role = role_id where users.enable = '1' and roles.\"createuser\" is not NULL ";
            $count = $this->_db->GetOne($countSql);
            return DB::returnModelRes(['data'=>$res,'count'=>$count])[0];
        }

    }

    /**
     * 用户统计
     * @return array
     */
    function userSta(){
        $sqlTotal = "select count(*) from ea_user";
        $sqlUnv = "select count(*) from ea_user where valued = '0'";
        $sqlSingle = "select count(*) from ea_user where valued = '1'";
        $sqlArt = "select count(*) from ea_user where valued = '2'";

        $total = $this->_db->GetOne($sqlTotal);
        $unverify = $this->_db->GetOne($sqlUnv);
        $single = $this->_db->GetOne($sqlSingle);
        $artificial = $this->_db->GetOne($sqlArt);
        return ['total'=>$total,'unverify'=>$unverify,'single'=>$single,'artificial'=>$artificial];
    }

    /**
     * 人员详情
     * @param $userId
     * @return array
     */
    function getUserDetail($userId){
        $sql = " select rolename,phone,email,ea_role.createtime,nickname,avatar from role_user LEFT JOIN ea_role on role_user.role = role_id where depart_user_id = ? ";
        $unSql = " select count(*) count from instance_log where verify_userid = ? and verify_time is null ";
        $finishedSql = " select count (*) count from instance_id where verify_userid = ? and verify_time is not null ";

        $detail = $this->_db->GetRow($sql,[$userId]);
//        echo $this->_db->errorMsg();
        $undeal = $this->_db->GetOne($unSql,[$userId]);
//        echo $this->_db->errorMsg();
        $finished = $this->_db->GetOne($finishedSql,[$userId]);
        return ['detail'=>$detail,'undeal'=>(int)$undeal,'finished'=>(int)$finished];
    }
}
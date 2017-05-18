<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/30
 * Time: 19:27
 */
class DepartmentModel{
    private $_db;
    function __construct()
    {
        $this->_db = DB::getInstance();
    }

    /**
     * 获取部门
     * @return array|bool|null
     */
    public function getAllDepartment($map = ''){
        if(func_num_args() >= 1){
            $params = array_slice(func_get_args(),1);
            $departSql = "select *,case when total_accept = 0 then 0 else round(finished/total_accept)*100 end as rate from ".DB::TB_ROLE." where enable = '1' and createuser is not null {$map} order by createtime desc";
            $res = $this->_db->GetAll($departSql,$params);
            return DB::returnModelRes($res)[0];
        }
        else{
            $departSql = "select *,case when total_accept = 0 then 0 else round(finished/total_accept)*100 end as rate from ".DB::TB_ROLE." where enable = '1' and createuser is not null order by createtime desc";
            $res = $this->_db->GetAll($departSql);
            return DB::returnModelRes($res)[0];
        }
    }

    /**
     * 建立部门
     * @param $name
     * @param $desc
     * @param $userId
     * @param $auth
     * @return bool|string
     */
    public function addDepartment($name,$desc,$userId,$auth){
        $existSql = 'select count(*) from '.DB::TB_ROLE.' where rolename = ? ';
        $existRes = $this->_db->GetOne($existSql,[$name]);
        if($existRes > 0){
            return 'exist';
        }
        $insertSql = 'insert into '.DB::TB_ROLE.'(rolename,role_desc,createtime,createuser,enable) values (?,?,?,?) returning role_id';
        $this->_db->BeginTrans();
        $insertRes = $this->_db->GetOne($insertSql,[$name,$desc,date('Y-m-d H:i:s'),$userId,1]);
        if(empty($auth) && $insertRes){
            $this->_db->CommitTrans();
            return true;
        }
        else if($insertRes == false){
            $this->_db->RollbackTrans();
            return false;
        }
        else if(!empty($auth) && $insertRes){
            $placeholders = substr(str_repeat('(?,?),',count($auth)),0,-1);
            $insertPerSql = "insert into ".DB::TB_ROLE_PERMISSION."(role,permission) values{$placeholders}";
            $params = [];
            foreach ($auth as $row){
                $params[] = $insertRes;
                $params[] = $row;
            }
            $insertPerRes = $this->_db->Execute($insertPerSql,$params);
            if($insertPerRes == false){
                $this->_db->RollbackTrans();
                return false;
            }
            $this->_db->CommitTrans();
            return true;
        }
    }

    function getDepartmentUsers($role){
        $sql = 'select * from role_user where role = ? and enable = \'1\' ';
        $users = $this->_db->GetAll($sql,[$role]);
        return DB::returnModelRes($users)[0];
    }
}
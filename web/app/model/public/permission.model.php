<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/27
 * Time: 11:53
 */

class PermissionModel{
    private $_db;
    function __construct()
    {
        $this->_db = DB::getInstance();
    }

    public function getRolePermission($role){

        $sql = "select permission,name,code,func,font,leaf,parent_id as parent from ".DB::TB_ROLE_PERMISSION." roles left join ".DB::TB_PERMISSION." permi 
                on roles.permission = permi.\"id\" 
                where roles.\"role\" = ? and \"enable\" = '1' ORDER BY sort_code asc";
        $res = $this->_db->GetAll($sql,[$role]);
        if($res == false){
            return [];
        }
        return $res;
    }
}
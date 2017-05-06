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

    /**
     * 获取角色权限
     * @param $role
     * @return array
     */
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

    /**
     * 获取权限列表
     * @return mixed
     */
    public function getPermissionList($map = ""){
        $sql = "select * from ".DB::TB_PERMISSION." where enable = '1' {$map} order by sort_code";
        $res = $this->_db->GetALl($sql);
        return DB::returnModelRes($res)[0];
    }
}
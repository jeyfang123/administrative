<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/27
 * Time: 14:28
 */
class DepartmentController extends Controller{

    public function render(){
        $permissionList = Box::getObject('permission','model','public')->getPermissionList(" code != '#' ");
        if($permissionList != true){
            $permissionList = [];
        }

        $department = Box::getObject('department','model','admin')->getAllDepartment();
        if($department != true){
            $department = [];
        }
        $departmentUser = Box::getObject('user','model','admin')->getDepartmentUser();
        if($departmentUser != true){
            $departmentUser = [];
        }
        foreach ($department as &$row){
            $row['user'] = [];
            foreach ($departmentUser as $item){
                if($row['role_id'] == $item['role']){
                    array_push($row['user'],$item);
                }
            }
        }

        $this->_twig->assign('data',['perList'=>$permissionList,'depart'=>$department]);
        return $this->viewTpl('department.html');
    }

    /**
     * 搜索相关部门
     * @param $req
     * @return mixed
     */
    public function searchDepartment($req){
        $departName = trim(Filtros::post_check($req->param('key')));
        $department = Box::getObject('department','model','admin')->getAllDepartment(' and rolename like ? ',["%{$departName}%"]);
        if($department === false){
            return $this->returnJson(['code'=>CODE_ERROR]);
        }
        else if($department === null){
            return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>[]]);
        }
        else {
            return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>$department]);
        }
    }

    /**
     * 获取所有部门
     */
    public function getDepartment(){
        $department = Box::getObject('department','model','admin')->getAllDepartment();
        if($department === false){
            return $this->returnJson(['code'=>CODE_ERROR]);
        }
        else if($department === null){
            return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>[]]);
        }
        else {
            return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>$department]);
        }
    }

    /**
     * 创建部门
     * @param $req
     * @return string
     */
    public function addDepartment($req){
        $user = $req->user;
        $departName = trim(Filtros::post_check($req->param('departName')));
        $departDesc = Filtros::post_check($req->param('departDesc'));
        $auth = Filtros::post_check($req->param('auth'));
        if(empty($departName) || empty($departDesc)){
            return $this->returnJson(['code'=>CODE_PARAMETER_ERROR,'msg'=>'部门信息不完整']);
        }
        $res = Box::getObject('department','model','admin')->addDepartment($departName,$departDesc,$user->depart_user_id,$auth);
        if($res === 'exist'){
            return $this->returnJson(['code'=>CODE_PARAMETER_ERROR,'msg'=>'该部门已存在']);
        }
        else if($res === false){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'创建失败']);
        }
        else if($res === true){
            return $this->returnJson(['code'=>CODE_SUCCESS]);
        }
    }
}
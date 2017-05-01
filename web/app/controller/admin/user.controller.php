<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/17
 * Time: 11:54
 */

class UserController extends Controller{

    public function login(){
        return $this->viewTpl ('login.html');
    }

    function doLogin($req){
        $username = $req->param('username','');
        $password = $req->param('password','');
        if(empty($username) || empty($password)){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'用户名或密码错误']);
        }
        $loginRes = Box::getObject('user','model','admin')->login($username,$password);
        if($loginRes == false){
            return $this->returnJson(['code'=>CODE_NOT_FOUND,'msg'=>'用户名或密码错误']);
        }
        $token = Box::getObject('token', 'model', 'public')->getToken($loginRes["role"],json_encode($loginRes));
        return $this->returnJson(['code'=>CODE_SUCCESS,'token'=>$token]);
    }

    /**
     * 部门人员
     * @return mixed
     */
    function roleuser(){
        $department = Box::getObject('department','model','admin')->getAllDepartment();
        if($department != true){
            $department = [];
        }
        $this->_twig->assign('data',['department'=>$department]);
        return $this->viewTpl ('roleuser.html');
    }

    /**
     * 添加部门人员
     * @param $req
     * @return string
     */
    function addRoleuser($req){
        $username = trim(Filtros::post_check($req->param('username')));
        $role = $req->param('role');
        if(empty($username) || strlen($role) != 36){
            return $this->returnJson(['code'=>CODE_PARAMETER_ERROR,'msg'=>'信息不完整']);
        }
        $res = Box::getObject('user','model','admin')->addRoleuser($role,$username);
        if($res === 'exist'){
            return $this->returnJson(['code'=>CODE_Already_Registered,'msg'=>'该账户已存在']);
        }
        else if($res === false){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'添加失败']);
        }
        else{
            return $this->returnJson(['code'=>CODE_SUCCESS]);
        }
    }



}
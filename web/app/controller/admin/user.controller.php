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
        $_COOKIE['token'] = $token;
        return $this->returnJson(['code'=>CODE_SUCCESS,'token'=>$token]);
    }

    /**
     * 部门人员
     * @return mixed
     */
    function roleuser(){
        $department = Box::getObject('department','model','admin')->getAllDepartment();
        $total = Box::getObject('user','model','admin')->getTotalRoleUser();
        if($department != true){
            $department = [];
        }
        $this->_twig->assign('data',['department'=>$department,'userTotal'=>$total]);
        return $this->viewTpl ('roleuser.html');
    }

    /**
     * 获取所有部门人员（搜索）
     * @param $req
     * @return string
     */
    function getRoleUser($req){
        $keyword = Filtros::post_check($req->param('keywords',''));
        $page = (int)$req->param('page',1);
        $pageSize = (int)$req->param('pagesize',10);
        $res = Box::getObject('user','model','admin')->getRoleUser($keyword,$page,$pageSize);
        if($res === false){
            return $this->returnJson(['code'=>CODE_ERROR]);
        }
        else if($res === null){
            return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>[],'count'=>0]);
        }
        else {
            return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>$res['data'],'count'=>$res['count']]);
        }
    }

    /**
     * 添加部门人员
     * @param $req
     * @return string
     */
    function addRoleuser($req){
        $username = trim(Filtros::post_check($req->param('username')));
        $role = $req->param('role');
        $avatar = $req->param('avatar');
        if(empty($username) || strlen($role) != 36 || empty($avatar)){
            return $this->returnJson(['code'=>CODE_PARAMETER_ERROR,'msg'=>'信息不完整']);
        }
        $res = Box::getObject('user','model','admin')->addRoleuser($role,$username,$avatar);
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
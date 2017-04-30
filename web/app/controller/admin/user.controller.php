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



}
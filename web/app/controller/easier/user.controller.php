<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/5/15
 * Time: 21:32
 */
class UserController extends Controller{

    /**
     * @param $req
     * @return string
     */
    function login($req){
        $username = Filtros::post_check($req->param('username'));
        $password = Filtros::post_check($req->param('password'));
        $user = Box::getObject('user','model','easier')->getUser($username,$password);
        if(empty($user)){
            return $this->returnJson(['code'=>CODE_NOT_FOUND]);
        }
        $access = Box::getObject('token','model','public')->getToken('easier',json_encode($user[0]));
        return $this->returnJson(['code'=>CODE_SUCCESS,'access'=>$access]);
    }

    function register($req){
        $username = Filtros::post_check($req->param('username'));
        $password = Filtros::post_check($req->param('password'));
        if(empty($username) || empty($password)){
            return $this->returnJson(['code'=>CODE_PARAMETER_ERROR,'msg'=>'请填写用户名密码']);
        }
        $userObj = Box::getObject('user','model','easier');
        $check = $userObj->checkUsernameUnique($username);
        if($check == 0){
            $res = $userObj->addUser($username,$password);
            if($res == false){
                return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'服务器出错了']);
            }
            else{
                $user = $userObj->getUser($username,$password);
                $access = Box::getObject('token','model','public')->getToken('easier',json_encode($user[0]));
                return $this->returnJson(['code'=>CODE_SUCCESS,'access'=>$access]);
            }
        }
        else{
            return $this->returnJson(['code'=>CODE_Already_Registered,'msg'=>'该用户名已注册']);
        }
    }

    function checkUser($req){
        $access = $req->param('access');
        if(empty($access)){
            return $this->returnJson(['code'=>CODE_RELOGIN]);
        }
        $user = Box::getObject('token','model','public')->getUser($access);
        if($user == false){
            return $this->returnJson(['code'=>CODE_RELOGIN]);
        }
        else{
            return $this->returnJson(['code'=>CODE_USER_HAVELOGIN,'user'=>$user]);
        }
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/20
 * Time: 9:48
 */

class PermissionController extends Controller{

    public function checkLogin($req){
        $token = $req->param('token','');
        if(empty($token)){
            return $this->returnJson(['code'=>CODE_RELOGIN]);
        }
        $user = Box::getObject('token','model','public')->getUser($token);
        if($user == false){
            return $this->returnJson(['code'=>CODE_RELOGIN]);
        }
        return $this->returnJson(['code'=>CODE_USER_HAVELOGIN,'user'=>$user]);
    }

    public function checkPermission($req,$user){
        $token = $req->param('token', '');
        if(!$user && substr($token, 0, 4) === SUPER_ADMIN) {
            $user = Box::getObject('Admin','model','admin')->getUser($token);
            $req->user = $user;
            return true;
        }
        if(!$user){
            echo json_encode(['code' => CODE_RELOGIN,'msg' => 'token已过期']);
            exit();
        }
        $req->user = $user;
        return true;
    }
}
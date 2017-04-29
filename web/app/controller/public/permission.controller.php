<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/20
 * Time: 9:48
 */

class PermissionController extends Controller{
    private $_tokenObj = null;

    public function __construct(){
        parent::__construct();
        if($this->_tokenObj == null){
            $this->_tokenObj = Box::getObject('token','model','public');
        }
    }

    public function checkLogin($req){
        $token = $req->param('token','');
        if(empty($token)){
            return $this->returnJson(['code'=>CODE_RELOGIN]);
        }
        $user = $this->_tokenObj->getUser($token);
        if($user == false){
            return $this->returnJson(['code'=>CODE_RELOGIN]);
        }
        return $this->returnJson(['code'=>CODE_USER_HAVELOGIN,'user'=>$user]);
    }

    public function checkPermission($req){
        $user = $req->user;
        if(!$user){
            echo json_encode(['code' => CODE_RELOGIN,'msg' => 'token已过期']);
            exit();
        }
        $requestURL = \Klein\Request::createFromGlobals()->pathname();
        $permissionConfig = json_decode($this->getPermission($req),true);
        if(in_array($requestURL,$permissionConfig['path']) || @$GLOBALS['permissionConf']['path'][$requestURL]){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * 从redis获取当前角色权限
     * 若redis不存在则重新获取并存储
     * @param $req
     * @return array|string
     */
    public function getPermission($req = null,$user = null){
        if($user == null){
            $user = $req->user;
        }
        if(strlen($user->role) != 36){
            return [];
        }
        $this->_tokenObj->redisDel(($user->role).'-path');
        $this->_tokenObj->redisDel(($user->role).'-full');
        $permissionPath = $this->_tokenObj->getUser(($user->role).'-path');
        $fullPer = $this->_tokenObj->getUser(($user->role).'-full');

        if($permissionPath == false){
            $permissionFull = Box::getObject('permission','model','public')->getRolePermission($user->role);
            $path = array_column($permissionFull,'code');
            $savePerPath = $this->_tokenObj->set(($user->role).'-path',json_encode($path,JSON_UNESCAPED_UNICODE),6*60*60);
            $savePerFull = $this->_tokenObj->set(($user->role).'-full',json_encode($permissionFull,JSON_UNESCAPED_UNICODE),6*60*60);
            if($savePerFull == false){
                echo 'The redis has an error';
                die();
            }

            return $this->returnJson(['path'=>$path,'full'=>$permissionFull]);
        }

        return $this->returnJson(['path'=>$permissionPath,'full'=>$fullPer]);
    }

    /**
     * 刷新菜单（权限）
     */
    public function refreshPermission(){
        $keys = $this->_tokenObj->keys('keys ');
        foreach ($keys as $row){
            if(substr($row,-4) == 'full' || substr($row,-4) == 'path'){
                $this->_tokenObj->redisDel($row);
            }
        }
    }
}
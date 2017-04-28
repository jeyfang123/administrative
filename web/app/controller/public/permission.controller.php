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
        if(!$user){
            echo json_encode(['code' => CODE_RELOGIN,'msg' => 'token已过期']);
            exit();
        }
        $req->user = $user;
        $requestURL = \Klein\Request::createFromGlobals()->pathname();
        $permission = json_decode($this->getPermission($req),true);
        if(in_array($requestURL,$permission['path'])){
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
    public function getPermission($req){
        $user = $req->user;
        if(strlen($user['role']) != 36){
            return [];
        }
        $permission = json_decode(Box::getObject('token','model','public')->get($user['role'].'-path'),true);
        $fullPer = json_decode(Box::getObject('token','model','public')->get($user['role'].'-full'),true);

        if($permission == false){
            $permission = Box::getObject('permission','model','public')->getRolePermission($user['role']);
            $path = array_column($permission,'code');

            $savePerPath = Box::getObject('token','model','public')->set($user['role'].'-path',json_encode($path,JSON_UNESCAPED_UNICODE),6*60*60);
            $savePerFull = Box::getObject('token','model','public')->set($user['role'].'-full',json_encode($permission,JSON_UNESCAPED_UNICODE),6*60*60);
            if($savePerPath == false){
                echo 'The redis has an error';
            }

            return $this->returnJson(['path'=>$path,'full'=>$permission]);
        }

        return $this->returnJson(['path'=>$permission,'full'=>$fullPer]);
    }

    /**
     * 刷新菜单（权限）
     */
    public function refreshPermission(){
        $redis = Box::getObject('token','model','public');
        $keys = $redis->keys('keys ');
        foreach ($keys as $row){
            if(substr($row,-4) == 'full' || substr($row,-4) == 'path'){
                $redis->redisDel($row);
            }
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/10
 * Time: 16:56
 */

class IndexController extends Controller{

    function __construct(){
        parent::__construct();
    }

    private function getMenu($data){
        $menu = [];
        $item = ['first'=>'','second'=>'','third'=>[]];
        $length = count($data);
        for($i = 0; $i<$length; $i++){
            if($data[$i]['code'] == "#" && $data[$i]['parent'] == ''){
                array_push($menu,$item);
                $item = ['first'=>'','second'=>[],'third'=>[]];
                $item['first'] = $data[$i];
            }
            else if($data[$i]['code'] == "#" && $data[$i]['parent'] == $item['first']['permission']){
                $item['second'] = $data[$i];
            }
            else if($data[$i]['code'] != '#' && $data[$i]['parent'] == $item['second']['permission']){
                array_push($item['third'], $data[$i]);
            }
            else if($data[$i]['code'] != '#' && $data[$i]['parent'] == $item['first']['permission']){
                array_push($item['second'], $data[$i]);
            }
        }
        array_push($menu,$item);
        $menu = array_filter($menu);
        return $menu;
    }

    function render($req){
        $user = $req->user;
        $permissionObj = Box::getObject('permission','controller','public');
//        $permissionObj->refreshPermission();
        $permission = json_decode($permissionObj->getPermission($req),'true');
        $menu = $this->getMenu($permission['full']);
        print_r($menu);
        $this->_twig->assign('data',['user'=>$user,'menu'=>$permission['full']]);
        return $this->viewTpl ('index.html');
    }

    function home(){
        return $this->viewTpl ('home.html');
    }
}
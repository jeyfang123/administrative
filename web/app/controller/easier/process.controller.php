<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/5/13
 * Time: 16:06
 */
class ProcessController extends Controller{

    function index($req){
        $type = $req->param('type');
        $department = Box::getObject('department','model','admin')->getAllDepartment();
        $proTypes = Box::getObject('process','model','admin')->getProType();
        if($type == 'person'){
            $proType = $proTypes['per'];
        }
        else if($type == 'enter'){
            $proType = $proTypes['enter'];
        }
        else{
            $proType = array_merge($proTypes['per'],$proTypes['enter']);
        }
        $this->_twig->assign('data',['proType'=>$proType,'department'=>$department]);
        return $this->viewTpl('process.html');
    }

    function searchProcess($req){
        $type = $req->param('type');
        $index = $req->param('index');
        $keyWords = $req->param('keyWords','');

        $page = $req->param('page',1);
        $pageSize = $req-param('pageSize',10);
        $map = " pro_name like ? ";
        $processObj = Box::getObject('process','model','easier');
        if($type == '-1'){
            $res = $processObj->searchProcess($page,$pageSize,$map,"%{$keyWords}%");
        }
        else if($type == 'depart'){
            $map .= " and pro_id in (select DISTINCT(pro_id) from process_node where role = ?) ";
            $res = $processObj->searchProcess($page,$pageSize,$map,"%{$keyWords}%",$index);
        }
        else if($type == 'type'){
            $map .= " and pro_type = ? ";
            $res = $processObj->searchProcess($page,$pageSize,$map,"%{$keyWords}%",$index);
        }
        if($res == false){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'查询失败']);
        }
        else if($res == null){
            return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>[],'count'=>0]);
        }
        return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>$res['data'],'count'=>$res['count']]);

    }
}
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
        $pageType = $req->param('pageType');

        $page = $req->param('page',1);
        $pageSize = $req->param('pageSize',10);
        $map = " pro_name like ? ";
        $processObj = Box::getObject('process','model','easier');
        if($type == '-1'){
            if($pageType == 'person'){
                $map .= " and types = ? ";
                $res = $processObj->searchProcess($page,$pageSize,$map,"%{$keyWords}%",1);
            }
            else if($pageType == 'enter'){
                $map .= " and types = ? ";
                $res = $processObj->searchProcess($page,$pageSize,$map,"%{$keyWords}%",2);
            }
            else{
                $res = $processObj->searchProcess($page,$pageSize,$map,"%{$keyWords}%");
            }

        }
        else if($type == 'depart'){
            $map .= " and pro_id in (select DISTINCT(pro_id) from process_node where role = ?) ";
            if($pageType == 'person'){
                $map .= " and types = ? ";
                $res = $processObj->searchProcess($page,$pageSize,$map,"%{$keyWords}%",$index,1);
            }
            else if($pageType == 'enter'){
                $map .= " and types = ? ";
                $res = $processObj->searchProcess($page,$pageSize,$map,"%{$keyWords}%",$index,2);
            }
            else{
                $res = $processObj->searchProcess($page,$pageSize,$map,"%{$keyWords}%",$index);
            }
        }
        else if($type == 'type'){
            $map .= " and pro_type = ? ";
            if($pageType == 'person'){
                $map .= " and types = ? ";
                $res = $processObj->searchProcess($page,$pageSize,$map,"%{$keyWords}%",$index,1);
            }
            else if($pageType == 'enter'){
                $map .= " and types = ? ";
                $res = $processObj->searchProcess($page,$pageSize,$map,"%{$keyWords}%",$index,2);
            }
            else{
                $res = $processObj->searchProcess($page,$pageSize,$map,"%{$keyWords}%",$index);
            }
        }
        if($res == false){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'查询失败']);
        }
        else if($res == null){
            return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>[],'count'=>0]);
        }
        return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>$res['data'],'count'=>$res['count']]);

    }

    /**
     * 事项详情
     * @param $req
     * @return string
     */
    function proDetail($req){
        $proId = $req->param('proId');
        $res = Box::getObject('process','model','easier')->proDetail($proId);
        if($res == false){
            echo '服务器错误';
            exit();
        }
        $res['detail']['acc_conditions'] = htmlspecialchars_decode($res['detail']['acc_conditions']);
        $res['detail']['exercise_basis'] = htmlspecialchars_decode($res['detail']['exercise_basis']);
        array_walk($res['flow'],function(&$item){
            $item['material'] = json_decode($item['material'],true);
        });
        $this->_twig->assign('data',['detail'=>$res['detail'],'flow'=>$res['flow']]);
        return $this->viewTpl('prodetail.html');
    }

    /**
     * 下载附件
     * @param $req
     */
    function download($req){
        $url = $req->param('url');
        $url = WEB_ROOT.$url;
        if(file_exists($url)){
            $extension = pathinfo($url,PATHINFO_EXTENSION);
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=附件.{$extension}");
            readfile($url);
        }
        else{
            echo 'no file';
        }
    }

    /**
     * 发起申请
     * @param $req
     * @return string
     */
    function apply($req){
        $access = $req->param('access');
        if(empty($access)){
            return $this->returnJson(['code'=>CODE_RELOGIN]);
        }
        $user = Box::getObject('token','model','public')->getUser($access);
        if($user == false){
            return $this->returnJson(['code'=>CODE_RELOGIN]);
        }
    }
}
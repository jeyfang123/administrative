<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/5/4
 * Time: 10:56
 */
class ProcessController extends Controller{

    /**
     * 事件类型
     * @return string
     */
    function getProType(){
        $res = Box::getObject('process','model','admin')->getProType();
        return $this->returnJson($res);
    }

    /**
     * 添加事项
     * @param $req
     * @return array|string
     */
    function addProcess($req){
        $processName = Filtros::post_check($req->param('examinename'));
        $examineContent = Filtros::post_check($req->param('examinecontent'));
        $contact = $req->param('contact');
        $supervise = $req->param('supervise');
        $exerciseBasis = Filtros::post_check($req->param('exercise_basis'));
        $perType = $req->param('perType');
        $enterType = $req->param('enterType');
        $accConditions = Filtros::post_check($req->param('acc_conditions'));
        $fee = Filtros::post_check($req->param('fee'));
        $term = Filtros::post_check($req->param('term'));
        $flowImg = $req->param("flow_img");
        $appendix = $req->param("appendix");

        $nodes = $req->param('nodes');

        if (!preg_match('/^(1[3578]\d{9})|(010\d{8})$/u', $contact)) {
            return ['code' => CODE_PARAMETER_ERROR, 'msg' => '联系方式错误'];
        }
        else if(!preg_match('/^(1[3578]\d{9})|(010\d{8})$/u', $supervise)){
            return $this->returnJson(['code'=>CODE_PARAMETER_ERROR,'msg'=>'监督电话填写错误']);
        }
        else if($perType == '-1' && $enterType == '-1'){
            return $this->returnJson(['code'=>CODE_PARAMETER_ERROR,'msg'=>'未选择类型']);
        }
        $process = [
            'processName'=>$processName,
            'examineContent'=>$examineContent,
            'exerciseBasis'=>$exerciseBasis,
            'accConditions'=>$accConditions,
            'fee'=>$fee,
            'term'=>$term,
            'contact'=>$contact,
            'supervise'=>$supervise,
            'perType'=>$perType,
            'enterType'=>$enterType,
            'flowImg'=>$flowImg,
            'appendix'=>$appendix
        ];
        $res = Box::getObject('process','model','admin')->addProcess($process,$nodes,$req->user->depart_user_id);
        if($res == false){
            return $this->returnJson(['code' => CODE_ERROR,'msg'=>'服务器错误，请稍后重试']);
        }
        return $this->returnJson(['code' => CODE_SUCCESS]);
    }

    function proList($req){
        $user = $req->user;
        $page = $req->param('page',1);
        $pageSize = $req->param('pageSize',15);
        $type = $req->param('type');
        $keyWords = $req->param('keywords');
        $adminRole = ['c6b813da-2ef4-439d-a8b1-9ae019352ff1','664e38e7-3a58-4bf2-9b6a-60614f105bb7','c6a919ac-0191-46f2-b938-84a387596ec8'];
        if(in_array($user->role,$adminRole)){

        }
        else{

        }
    }
}
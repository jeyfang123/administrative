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

    }
}
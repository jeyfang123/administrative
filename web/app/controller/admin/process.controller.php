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
        $info = $req->param('');
        return $this->returnJson(['code'=>CODE_SUCCESS]);
    }
}
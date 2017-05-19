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

    function render($req){
        $user = $req->user;
        $token = $req->param('token');
        $worker = Box::getObject('process','model','admin')->getPendingCount($user->depart_user_id,$user->role);
        $this->_twig->assign('data',['user'=>$user,'token'=>$token,'worker'=>$worker]);
        return $this->viewTpl ('index.html');
    }

    function home(){
        $obj = Box::getObject('process','model','admin');
        $total = $obj->totalInstance();
        $totalAccept = $obj->totalAccept();
        $totalFinished = $obj->totalFinished();
        $totalUnFinished = $obj->totalUnFinished();
        $eaUser = Box::getObject('user','model','admin')->userSta();
        if($eaUser['unverify'] == 0){
            $eaUser['unverifyRate'] = 0;
        }
        else{
            $eaUser['unverifyRate'] = round($eaUser['unverify']/$eaUser['total'])*100;
        }
        if($eaUser['single'] == 0){
            $eaUser['singleRate'] = 0;
        }
        else{
            $eaUser['singleRate'] = round($eaUser['single']/$eaUser['total'])*100;
        }
        if($eaUser['artificial'] == 0){
            $eaUser['artificialRate'] = 0;
        }
        else{
            $eaUser['artificialRate'] = round($eaUser['artificial']/$eaUser['total'])*100;
        }
        $this->_twig->assign('data',[
            'total'=>$total,
            'accept'=>$totalAccept,
            'finished'=>$totalFinished,
            'unfinished'=>$totalUnFinished,
            'eaUser'=>$eaUser
        ]);
        return $this->viewTpl ('home.html');
    }

    function lastPeriodSta(){
        $res = Box::getObject('process','model','admin')->lastPeriodSta();
        return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>$res]);
    }

    function departSta(){
        $res = Box::getObject('process','model','admin')->departSta();
        return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>$res]);
    }
}
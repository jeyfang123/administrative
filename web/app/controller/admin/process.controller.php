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
        $flowImg = $req->param("flow_img",null);
        $appendix = $req->param("appendix",null);

        $nodes = $req->param('nodes');


        if (!preg_match('/^(1[3578]\d{9})|(010\d{8})$/u', $contact)) {
            return $this->returnJson(['code' => CODE_PARAMETER_ERROR, 'msg' => '联系方式错误']);
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

    /**
     * 获取待受理事项
     * @param $req
     * @return string
     */
    function getProUnInstance($req){
        $access = $req->param('access');
        $insId = $req->param('insId','');
        $page= $req->param('page',1);
        $pageSize = $req->param('pageSize',15);
        $res = Box::getObject('process','model','admin')->getProUnInstance($insId,$page,$pageSize);
        if($res === false){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'服务器出错了']);
        }
        else if($res === null){
            return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>[],'count'=>0]);
        }
        else{
            return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>$res['data'],'count'=>$res['count']]);
        }
    }

    /**
     * 获取事项材料
     * @param $req
     * @return string
     */
    function getMaterial($req){
        $proId = $req->param('proId');
        $res = Box::getObject('process','model','admin')->getMaterial($proId);
        if($res){
            $material = [];
            foreach ($res as $row){
                $row = json_decode($row['material'],true);
                $material = array_merge($material,$row);
            }
            $material = array_unique($material);
            $index = array_search('无',$material);
            if($index !== false){
                array_splice($material,$index,1);
            }
            return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>$material]);
        }
        return $this->returnJson(['code'=>CODE_NOT_FOUND]);
    }

    /**
     * 受理事项
     * @param $req
     * @return string
     */
    function acceptPro($req){
        $token = $req->param('token');
        $insId = $req->param('insId');
        $material = $req->param('material');
        $obj =  Box::getObject('process','model','admin');
        $checkRes = $obj->checkAccept($insId);
        if($checkRes !== '0'){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'该事项已被他人受理']);
        }
        //受理事项，并写入审批记录，返回审批角色，审批用户
        $res = $obj->acceptPro($insId,$material);
        if($res === 'noUser'){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'受理失败，请联系管理员']);
        }
        else if($res === false){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'服务器错误']);
        }
        else{
            $info = $obj->getProUser($insId);
            $time = date('H:i:s');
            $messageObj = Box::getObject('message','controller','public');
            $toUser = $res['user'];
            $content = [
                'proName'=>$info['pro_name'],
                'fromUser'=>($info['compellation'] ? $info['compellation'] : $info['artificial']),
                'time'=>$time
            ];
            $messageObj->systemToWorker($toUser,$content);
            return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>$res]);
        }
    }

    private function planUserToPro($role,$user,$insInfo){
        $obj =  Box::getObject('process','model','admin');
        $res = $obj->planUserToPro($role,$user,$insInfo);
    }

    /**
     * 获取个人待审批事件
     * @param $req
     * @return string
     */
    function pending($req){
        $token = $req->param('token');
        $insId = $req->param('insId','');
        $userId = $req->user->depart_user_id;
        $res = Box::getObject('process','model','admin')->getOwnPending($userId,$insId);
        if($res === false){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'服务器错误']);
        }
        return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>$res]);
    }

    /**
     * 获取事项详情
     * @param $req
     * @return string
     */
    function getProInfo($req){
        $insId = $req->param("insId");
        $proId = $req->param("proId");
        $role = $req->user->role;
        $obj = Box::getObject('process','model','admin');
        //用户信息
        $userAndProInfo = $obj->getProUser($insId);
        $userAndProInfo['acc_conditions'] = htmlspecialchars_decode($userAndProInfo['acc_conditions']);
        $userAndProInfo['exercise_basis'] = htmlspecialchars_decode($userAndProInfo['exercise_basis']);
        $material = json_decode($obj->getCurNodeMaterial($proId,$role),true);
//        var_dump($userAndProInfo);
//        var_dump($material);

        if($userAndProInfo == false || $material == false){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'服务器错误']);
        }
        return $this->returnJson(['code'=>CODE_SUCCESS,'pro'=>$userAndProInfo,'material'=>$material]);
    }

    /**
     * 审核拒绝
     * @param $req
     * @return string
     */
    function denyPro($req){
        $insId = $req->param('insId');
        $denyMsg = $req->param('denyMsg');
        $logId = $req->param('logId');
        $user = $req->user;
        $obj = Box::getObject('process','model','admin');
        $res = $obj->denyPro($denyMsg,$logId,$insId);
        if($res == false){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'服务器错误']);
        }
        $obj->updateRoleFinished($user->role);
        return $this->returnJson(['code'=>CODE_SUCCESS]);
    }

    /**
     * 同意申请
     * @param $req
     * @return string
     */
    function agreePro($req){
        $insId = $req->param('insId');
        $logId = $req->param('logId');
        $proId = $req->param('proId');
        $user = $req->user;
        $obj = Box::getObject('process','model','admin');
        $res = $obj->agreePro($proId,$user->role,$logId,$insId);
        if($res == false){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'服务器错误']);
        }
        else if($res === 'noUser'){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'请求失败']);
        }
        else{
            $obj->updateRoleFinished($user->role);
            return $this->returnJson(['code'=>CODE_SUCCESS]);
        }
    }

    /**
     * 获取进行中事项
     * @param $req
     * @return string
     */
    function getProInstanceIng($req){
        $res = Box::getObject('process','model','admin')->getProInstanceIng('','',1,100);
        return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>$res['data']]);
    }

    /**
     * 获取结束事项
     * @param $req
     * @return string
     */
    function getProInstanceEd($req){
        $res = Box::getObject('process','model','admin')->getProInstanceEd('','',1,100);
        return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>$res['data']]);
    }
}
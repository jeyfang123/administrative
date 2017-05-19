<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/5/15
 * Time: 21:32
 */
class UserController extends Controller{

    /**
     * @param $req
     * @return string
     */
    function login($req){
        $username = Filtros::post_check($req->param('username'));
        $password = Filtros::post_check($req->param('password'));
        $user = Box::getObject('user','model','easier')->getUser($username,$password);
        if(empty($user)){
            return $this->returnJson(['code'=>CODE_NOT_FOUND]);
        }
        $access = Box::getObject('token','model','public')->getToken('easier',json_encode($user[0]));
        return $this->returnJson(['code'=>CODE_SUCCESS,'access'=>$access]);
    }

    function register($req){
        $username = Filtros::post_check($req->param('username'));
        $password = Filtros::post_check($req->param('password'));
        if(empty($username) || empty($password)){
            return $this->returnJson(['code'=>CODE_PARAMETER_ERROR,'msg'=>'请填写用户名密码']);
        }
        $userObj = Box::getObject('user','model','easier');
        $check = $userObj->checkUsernameUnique($username);
        if($check == 0){
            $res = $userObj->addUser($username,$password);
            if($res == false){
                return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'服务器出错了']);
            }
            else{
                $user = $userObj->getUser($username,$password);
                $access = Box::getObject('token','model','public')->getToken('easier',json_encode($user[0]));
                return $this->returnJson(['code'=>CODE_SUCCESS,'access'=>$access]);
            }
        }
        else{
            return $this->returnJson(['code'=>CODE_Already_Registered,'msg'=>'该用户名已注册']);
        }
    }

    function checkUser($req){
        $access = $req->param('access');
        if(empty($access)){
            return $this->returnJson(['code'=>CODE_RELOGIN]);
        }
        $user = Box::getObject('token','model','public')->getUser($access);
        if($user == false){
            return $this->returnJson(['code'=>CODE_RELOGIN]);
        }
        else{
            return $this->returnJson(['code'=>CODE_USER_HAVELOGIN,'user'=>$user]);
        }
    }

    function verifyCode(){
        include_once (dirname(__DIR__).'/public/VerifyCode.php');
        $verifyCode = new VerifyCode(200,50,VerifyCode::VERIFY_NUMBER,4);
        $verifyCode->printImage();
    }

    function checkVerifyCode($req){
        $code = $req->param('verify');
        if($code !== $_SESSION['verifyCode']){
            return $this->returnJson(['code'=>CODE_NOT_FOUND]);
        }
        else{
            return $this->returnJson(['code'=>CODE_SUCCESS]);
        }
    }

    function sendCode($req){
        session_start();
        $mobile = $req->param('phone');
        if(!preg_match('/^1[3578]\d{9}$/',$mobile)){
            return $this->returnJson(['code'=>CODE_ERROR, 'msg'=>'手机号码错误'],JSON_UNESCAPED_UNICODE);
        }
        $code = rand(1000,9999);
        $_SESSION['code'] = $code;
        $content = '【行政服务中心】您的验证码是：'.$code;
        $param = http_build_query(['mobile'=>$mobile,'content'=>$content,'tag'=>2]);
        $ch = curl_init();
        $url = 'http://apis.baidu.com/kingtto_media/106sms/106sms?'.$param;
        $header = array(
            'apikey: 2ce2b074e14f14b1f93db013814a77a2',
        );
        curl_setopt($ch , CURLOPT_URL , $url);
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        $res = curl_exec($ch);
        $res = json_decode($res,true);
        if($res['returnstatus']== 'Success'){
            return $this->returnJson(['code'=>CODE_SUCCESS]);
        }
        else{
            return $this->returnJson(['code'=>CODE_ERROR]);
        }
    }

    function perValue($req){
        $access = $req->param('access');
        $user = Box::getObject('token','model','public')->getUser($access);
        if($user == false){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'登录过期，请重新登录']);
        }
        $phone = $req->param('phone');
        $code = $req->param('code');
        $checkPhone = Box::getObject('user','model','easier')->checkPhoneUnique($phone);
        if($checkPhone != 0){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'该手机号已被注册']);
        }
        $compellation = $req->param('compellation');
        $id = strtoupper($req->param('id'));
        if($code != $_SESSION['code']){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'验证码错误']);
        }
        if(!preg_match('/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/',$id)){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'验证失败，身份证与本人不符']);
        }
        $res = Box::getObject('user','model','easier')->perValue($phone,$compellation,$id,$user);
        if($res == 'exist'){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'重复认证']);
        }
        else if($res == false){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'服务器出错了']);
        }
        else{
            return $this->returnJson(['code'=>CODE_SUCCESS]);
        }
    }


    function enterValue($req){
        $access = $req->param('access');
        $user = Box::getObject('token','model','public')->getUser($access);
        if($user == false){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'登录过期，请重新登录']);
        }
        $phone = $req->param('phone');
        $checkPhone = Box::getObject('user','model','easier')->checkPhoneUnique($phone);
        if($checkPhone != 0){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'该手机号已被注册']);
        }
        $code = $req->param('code');
        $artificial = $req->param('artificial');
        $enterprise = $req->param('enterprise');
        $creditcode = $req->param('creditcode');
        if($code != $_SESSION['code']){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'验证码错误']);
        }
        if(strlen($creditcode) != 18){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'企业统一社会信用代码不存在']);
        }
        $res = Box::getObject('user','model','easier')->perValue($phone,$artificial,$enterprise,$creditcode,$user);
        if($res == 'exist'){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'重复认证']);
        }
        else if($res == false){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'服务器出错了']);
        }
        else{
            return $this->returnJson(['code'=>CODE_SUCCESS]);
        }
    }



}
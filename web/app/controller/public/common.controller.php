<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/17
 * Time: 11:47
 */

class Common extends Controller{
    public function returnControRes($res,$count = null){
        switch ($res){
            case CODE_ERROR :
                return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'请求失败,请重试']);
                break;
            case CODE_NOT_FOUND :
                return $this->returnJson(['code'=>CODE_NOT_FOUND,'msg'=>'未找寻到匹配的信息']);
                break;
            case CODE_SUCCESS :
                if($count === false){
                    return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'请求失败,请重试']);
                }
                return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>$res,'count'=>$count]);
                break;
        }
    }

    public function returnModelRes($res){
        if($res === false){
            return false;
        }
        else if(empty($res)){
            return null;
        }
        else{
            if(func_num_args() > 1){
                return func_num_args();
            }
            return $res;
        }
    }
}
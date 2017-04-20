<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/17
 * Time: 11:47
 */

class Common extends Controller{

    public function returnModelRes($res){
        if($res === false){
            return false;
        }
        else if(empty($res)){
            return null;
        }
        else{
            return func_num_args();
        }
    }
}
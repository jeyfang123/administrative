<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/17
 * Time: 11:47
 */

class Common extends Controller{
    public function noPermission(){
        return $this->viewTpl ('noPermission.html');
    }
}
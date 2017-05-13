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
        return $this->viewTpl('process.html');
    }
}
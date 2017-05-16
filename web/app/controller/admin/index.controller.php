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
        $this->_twig->assign('data',['user'=>$user,'token'=>$token]);
        return $this->viewTpl ('index.html');
    }

    function home(){
        return $this->viewTpl ('home.html');
    }
}
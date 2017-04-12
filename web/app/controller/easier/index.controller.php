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

    function render(){
        $res = Box::getObject('user','model','easier')->getUser('jeyfang','123456');
        if($res == 1){
            $this->_twig->assign('data','Hello Word');
            return $this->viewTpl ('index.html');
        }
        else{
            echo '未登录';
        }

    }
}
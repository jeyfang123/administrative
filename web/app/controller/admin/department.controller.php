<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/27
 * Time: 14:28
 */
class DepartmentController extends Controller{

    public function render(){
        return $this->viewTpl('department.html');
    }

    public function createDepart(){
        return $this->viewTpl('create_depart.html');
    }
}
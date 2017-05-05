<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/5/4
 * Time: 10:57
 */
class ProcessModel{
    private $_db;
    function __construct()
    {
        $this->_db = DB::getInstance();
    }

    /**
     * 获取事件类型
     * @return array
     */
    function getProType(){
        $perSql = "select * from ".DB::TB_TYPE." where types = '1' ";
        $enterSql = "select * from ".DB::TB_TYPE." where types = '2' ";
        $perRes = $this->_db->GetAll($perSql);
        $enterRes = $this->_db->GetAll($enterSql);
        return ['per'=>$perRes,'enter'=>$enterRes];
    }
}
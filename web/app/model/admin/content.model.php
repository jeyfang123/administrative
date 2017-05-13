<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/5/10
 * Time: 0:12
 */
class ContentModel{
    private $_db;
    function __construct()
    {
        $this->_db = DB::getInstance();
    }

    /**
     * 返回内容
     * @param $key
     * @param $type
     * @param $page
     * @param $pageSize
     * @return array|bool|null
     */
    function getContent($key = null,$type,$page,$pageSize){
        $offset = ($page - 1)*$pageSize;
        $map = '';
        if($key != null){
            $map = " and title like '%{$key}%' ";
        }
        $sql = "select contents.*,nickname from ".DB::TB_CONTENT." contents left join ".DB::TB_ROLE_USER." on contents.author = depart_user_id where type = ? {$map} order by publish_time desc offset {$offset} limit {$pageSize}";
        $countSql = "select count(*) count from ".DB::TB_CONTENT." where type = ? {$map} ";
        $res = $this->_db->GetAll($sql,[$type]);
        $count = $this->_db->GetOne($countSql,[$type]);
        return DB::returnModelRes(['data'=>$res,'total'=>$count])[0];
    }

    /**
     * @param $id
     * @return mixed
     */
    function contentDetail($id){
        $sql = "select contents.*,nickname from ".DB::TB_CONTENT." contents left join ".DB::TB_ROLE_USER." on contents.author = depart_user_id where id = ? ";
        $res = $this->_db->GetRow($sql,[$id]);
        $update = "update ".DB::TB_CONTENT." set viewpoint = viewpoint+1 where id = ? ";
        $this->_db->Execute($update,[$id]);
        return DB::returnModelRes($res)[0];
    }

    /**
     * 添加内容
     * @param $content
     * @param $userId
     * @return bool
     */
    function addContent($content,$userId){
        $publishTime = date('Y-m-d H:i:s');
        $sql = "insert into ".DB::TB_CONTENT."(title,contents,author,publish_time,type,cover,grandson) values(?,?,?,?,?,?,?)";
        $res = $this->_db->Execute($sql,[$content['title'],$content['content'],$userId,$publishTime,$content['type'],$content['cover'],$content['grandson']]);
        if($res == false){
            return false;
        }
        return true;
    }

    /**
     * 首页今日关注图片类
     * @return mixed
     */
    function indexAttImg(){
        $attImgSql = "select * from ".DB::TB_CONTENT." where cover is NOT NULL and type = '1' ORDER BY publish_time desc  LIMIT 4";
        $attImgRes = $this->_db->GetAll($attImgSql);
        return DB::returnModelRes($attImgRes)[0];
    }

    /**
     * 首页今日关注非图片类
     * @return mixed
     */
    function indexAtt(){
        $attImgSql = "select * from ".DB::TB_CONTENT." where cover is NULL and type = '1' ORDER BY publish_time desc  LIMIT 8";
        $attImgRes = $this->_db->GetAll($attImgSql);
        return DB::returnModelRes($attImgRes)[0];
    }

    /**
     * 首页政策解读
     * @return mixed
     */
    function indexPoli(){
        $poliSql = "select * from ".DB::TB_CONTENT." where type = '2' ORDER BY publish_time desc LIMIT 5";
        $poliRes = $this->_db->GetAll($poliSql);
        return DB::returnModelRes($poliRes)[0];
    }
}
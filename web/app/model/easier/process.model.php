<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/5/14
 * Time: 20:19
 */
class ProcessModel{
    private $_db;
    function __construct()
    {
        $this->_db = DB::getInstance();
    }

    function searchProcessHasDepart($role){
        $sql = "select DISTINCT(pro_id) from process_node 
                    where \"role\" = ? ";
        $res = $this->_db->GetAll($sql,[$role]);
        $sql = "pro_id in(
select DISTINCT(pro_id) from process_node 
where \"role\" = '7588a16a-d216-43e7-a97f-bbd2b46e6c30')";
    }

    function searchProcess($page=1,$pageSize=10,$map = null){
        $offset = ($page-1)*$pageSize;
        if($map == null){
            $sql = "select pro_name,pro_id,heatpoint from ".DB::TB_PROCESS." LEFT JOIN
                    (select lefttab.*, case when righttab.per_type = '-1' then lefttab.enter_type 
                        when righttab.enter_type = '-1' then lefttab.per_type end as pro_type 
                    from ".DB::TB_PRO_DETAIL." lefttab LEFT JOIN ".DB::TB_PRO_DETAIL." righttab on lefttab.pop_id = righttab.pop_id) pro_detail
                on pro_id = pop_id
                LEFT JOIN ".DB::TB_TYPE." on pro_type = type_id where 1=1 offset {$offset} limit {$pageSize}";
            $res = $this->_db->GetAll($sql);
            $count = "select count(*) count from ".DB::TB_PROCESS." LEFT JOIN
                    (select lefttab.*, case when righttab.per_type = '-1' then lefttab.enter_type 
                        when righttab.enter_type = '-1' then lefttab.per_type end as pro_type 
                    from ".DB::TB_PRO_DETAIL." lefttab LEFT JOIN ".DB::TB_PRO_DETAIL." righttab on lefttab.pop_id = righttab.pop_id) pro_detail
                on pro_id = pop_id
                LEFT JOIN ".DB::TB_TYPE." on pro_type = type_id where 1=1 ";
            $total = $this->_db->GetOne($count);
        }
        else{
            $params = array_slice(func_get_args(),3);
            $sql = "select pro_name,pro_id,heatpoint from ".DB::TB_PROCESS." LEFT JOIN
                    (select lefttab.*, case when righttab.per_type = '-1' then lefttab.enter_type 
                        when righttab.enter_type = '-1' then lefttab.per_type end as pro_type 
                    from ".DB::TB_PRO_DETAIL." lefttab LEFT JOIN ".DB::TB_PRO_DETAIL." righttab on lefttab.pop_id = righttab.pop_id) pro_detail
                on pro_id = pop_id
                LEFT JOIN ".DB::TB_TYPE." on pro_type = type_id where 1=1 and {$map} offset {$offset} limit {$pageSize}";
            $res = $this->_db->GetAll($sql,$params);
            $count = "select count(*) count from ".DB::TB_PROCESS." LEFT JOIN
                    (select lefttab.*, case when righttab.per_type = '-1' then lefttab.enter_type 
                        when righttab.enter_type = '-1' then lefttab.per_type end as pro_type 
                    from ".DB::TB_PRO_DETAIL." lefttab LEFT JOIN ".DB::TB_PRO_DETAIL." righttab on lefttab.pop_id = righttab.pop_id) pro_detail
                on pro_id = pop_id
                LEFT JOIN ".DB::TB_TYPE." on pro_type = type_id where 1=1 and {$map}";
            $total = $this->_db->GetOne($count,$params);
        }
        return DB::returnModelRes(['data'=>$res,'count'=>$total])[0];
    }

    /**
     * 事项详情
     * @param $proId
     * @return mixed
     */
    function proDetail($proId){
        $detailSql = "select * from ".DB::TB_PROCESS." LEFT JOIN
                            (select lefttab.*, case when righttab.per_type = '-1' then lefttab.enter_type 
                            when righttab.enter_type = '-1' then lefttab.per_type end as pro_type 
                            from ".DB::TB_PRO_DETAIL." lefttab LEFT JOIN ".DB::TB_PRO_DETAIL." righttab on lefttab.pop_id = righttab.pop_id) pro_detail
                    on pro_id = pop_id
                    LEFT JOIN process_type on pro_type = type_id where pro_id = ?";
        $detail = $this->_db->GetRow($detailSql,[$proId]);

        $flowSql = "SELECT process_node.*,ea_role.rolename FROM ".DB::TB_PRO_NODE."
                    LEFT JOIN ".DB::TB_PRO_FLOW." on process_flow.pro_id = process_node.pro_id and node_id = last_nodeid
                    LEFT JOIN ".DB::TB_ROLE." on ea_role.role_id = process_node.\"role\"
                WHERE
                    process_node.pro_id = ?
                ORDER BY flow_id asc";
        $flow = $this->_db->GetAll($flowSql,[$proId]);
        return DB::returnModelRes(['detail'=>$detail,'flow'=>$flow])[0];
    }
}
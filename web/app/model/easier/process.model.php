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

    /**
     * 发起申请
     * @param $proId
     * @param $proName
     * @param $userId
     * @return mixed
     */
    function apply($proId,$proName,$userId){
        $existSql = "select count(*) from ".DB::TB_INSTANCE." where pro_id = ? and create_userid = ? and status = ? ";
        $existRes = $this->_db->GetOne($existSql,[$proId,$userId,0]);
        if($existRes > 0){
            return 'exist';
        }
        $time = date("Y-m-d H:i:s");
        $sql = " insert into ".DB::TB_INSTANCE."(pro_id,pro_name,create_userid,create_time,status) values(?,?,?,?,0) returning pro_ins_id";
        return $this->_db->GetOne($sql,[$proId,$proName,$userId,$time]);
    }

    function checkApplyUserType($type,$proId){
        if($type == '1'){
            $sql = "select count(*) from process_detail where pop_id = ? and enter_type = '-1' ";
            $res = $this->_db->GetOne($sql,[$proId]);
            if($res != 0){
                return true;
            }
            else{
                return false;
            }
        }
        else if($type == '2'){
            $sql = "select count(*) from process_detail where pop_id = ? and per_type = '-1' ";
            $res = $this->_db->GetOne($sql,[$proId]);
            if($res != 0){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }

    }

    /**
     * 流动信息
     * @return mixed
     */
    function indexGetFlowProcess(){
        $sql = "select ins.*, case when ins.status = '1' then '受理' when ins.status = '2' then '办结' 
                      when ins.status = '3' then '拒结' end as stadesc,
                  roles.rolename,COALESCE(compellation,artificial) as username from process_instance ins
                LEFT JOIN ea_user on ins.create_userid = id
                LEFT JOIN instance_log log on log.log_id = ins.log_id
                LEFT JOIN ea_role roles on log.verify_role = roles.role_id
                where status != '0' ORDER BY create_time desc limit 15 ";
        $res = $this->_db->GetAll($sql);
        return $res;
    }
}
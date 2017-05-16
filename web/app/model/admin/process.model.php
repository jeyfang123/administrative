<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/5/4
 * Time: 10:57
 */
class ProcessModel{
    private $_db;
    private $adminRole = ['c6b813da-2ef4-439d-a8b1-9ae019352ff1','664e38e7-3a58-4bf2-9b6a-60614f105bb7','c6a919ac-0191-46f2-b938-84a387596ec8'];
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

    /**
     * 添加事项
     * @param $process
     * @param $userId
     * @return bool
     */
    private function addMainProcess($process,$userId){
        $time = date('Y-m-d H:i:s');
        $proId = rand(10,99).substr(time(),2);
        $sql = "insert into ".DB::TB_PROCESS."(pro_id,pro_name,pro_content,enable,create_date,create_userid) values(?,?,?,'1','{$time}',?)";
        $res = $this->_db->Execute($sql,[$proId,$process['processName'],$process['examineContent'],$userId]);
        if($res === false){
            return false;
        }
        return $proId;
    }

    private function addProDetail($process,$proId){
        $sql = "insert into ".DB::TB_PRO_DETAIL."(pop_id,per_type,enter_type,exercise_basis,acc_conditions,fee,term,contact,supervise_tel,flow_img,appendix)
                values(?,?,?,?,?,?,?,?,?,?,?)";
        $res = $this->_db->Execute($sql,[$proId,$process['perType'],$process['enterType'],$process['exerciseBasis'],$process['accConditions'],
            $process['fee'],$process['term'],$process['contact'],$process['supervise'],$process['flowImg'],$process['appendix']]);
        if($res === false){
            return false;
        }
        return true;
    }

    /**
     * 插入节点
     * @param $nodes
     * @param $proId
     * @return array|bool
     */
    private function addProNode($nodes,$proId){
        $sql = "insert into ".DB::TB_PRO_NODE."(pro_id,title,material,role) values ";
        $nodeCount = count($nodes);
        $placeholders = substr(str_repeat('(?,?,?,?),',$nodeCount),0,-1);
        $params = [];
        foreach ($nodes as $row){
            $params[] = $proId;
            $params[] = $row['nodeTitle'];
            $params[] = $row['nodeMaterial'];
            $params[] = $row['nodeRole'];
        }
        $sql = $sql.$placeholders.' returning node_id';
        $res = $this->_db->GetAll($sql,$params);
        if($res == false){
            return false;
        }
        return array_column($res,'node_id');
    }

    /**
     * 添加节点顺序
     * @param $node
     * @param $proId
     * @return bool
     */
    private function addProFlow($node,$proId){
        $sql = "insert into ".DB::TB_PRO_FLOW."(pro_id,last_nodeid,next_nodeid) values ";
        $nodeCount = count($node);
        $placeholders = substr(str_repeat('(?,?,?),',$nodeCount),0,-1);
        $params = [];
        for($i=0; $i<$nodeCount-1;$i++){
            $params[] = $proId;
            $params[] = $node[$i];
            $params[] = $node[$i+1];
        }
        $params[] = $proId;
        $params[] = $node[$nodeCount-1];
        $params[] = null;
        $sql = $sql.$placeholders;
        $res = $this->_db->Execute($sql,$params);
        if($res == false){
            return false;
        }
        return $res;
    }

    /**
     * 添加事项
     * @param $process
     * @param $node
     * @param $userId
     * @return bool
     */
    public function addProcess($process,$node,$userId){
        $proId = $this->addMainProcess($process,$userId);
        if($proId == false){
            return false;
        }
        $proDetail = $this->addProDetail($process,$proId);
        if($proDetail == false){
            return false;
        }
        $proNode = $this->addProNode($node,$proId);
        if($proNode == false){
            return false;
        }
        $proFlow = $this->addProFlow($proNode,$proId);
        if($proFlow == false){
            return false;
        }
        return true;
    }

    /**
     * 热点服务项目
     * @return mixed
     */
    public function getHeatPro(){
        $sql = "select ea_process.pro_name,pro_id from ".DB::TB_PROCESS." LEFT JOIN ".DB::TB_PRO_DETAIL." on pop_id = pro_id ORDER BY heatpoint desc limit 15";
        $res = $this->_db->GetAll($sql);
        return DB::returnModelRes($res)[0];
    }

    /**
     * 获取待发起事项
     * @param $keywords
     * @param $page
     * @param $pageSize
     * @return array|bool|null
     */
    public function getProUnInstance($keywords,$page,$pageSize){
        $map = '';
        if(!empty($keywords)){
            $map = "and pro_name like '%$keywords%' ";
        }
        $offset = ($page - 1)* $pageSize;
        $sql = "select * from process_instance ins left JOIN(
                    select rolename,flow.pro_id from (
                        select * from (select * ,row_number() over (PARTITION by pro_id ORDER BY flow_id asc ) as row_index from process_flow) innerflow where row_index =1) flow 
                    left JOIN process_node node on flow.last_nodeid = node.node_id
                LEFT JOIN ea_role on node.role = role_id) roles on ins.pro_id = roles.pro_id 
                where 1=1 and {$map} ORDER BY create_time desc offset {$offset} limit {$pageSize}";
        $count = "select count(*) from process_instance ins left JOIN(
                    select rolename,flow.pro_id from (
                        select * from (select * ,row_number() over (PARTITION by pro_id ORDER BY flow_id asc ) as row_index from process_flow) innerflow where row_index =1) flow 
                    left JOIN process_node node on flow.last_nodeid = node.node_id
                LEFT JOIN ea_role on node.role = role_id) roles on ins.pro_id = roles.pro_id 
                where 1=1 and {$map}";
        $res = $this->_db->GetAll($sql);
        $total = $this->_db->GetOne($count);
        return DB::returnModelRes(['data'=>$res,'count'=>$total]);
    }

    /**
     * 审核中事项
     * @param $map
     * @param $page
     * @param $pageSize
     * @return array|bool|null
     */
    private function proInstanceIng($map,$page,$pageSize){
        $offset = ($page - 1)* $pageSize;
        $sql = "select * from process_instance ins 
                  LEFT JOIN instance_log log on ins.log_id = log.log_id
                  LEFT JOIN ea_role roles on roles.role_id = verify_role
                where status = '1' {$map} ORDER BY ins.create_time desc offset {$offset} limit {$pageSize}";
        $count = "select count(*) from process_instance ins 
                    LEFT JOIN instance_log log on ins.log_id = log.log_id
                  where status = '1' {$map}";
        $res = $this->_db->GetAll($sql);
        $total = $this->_db->GetOne($count);
        return DB::returnModelRes(['data'=>$res,'count'=>$total]);
    }


    /**
     * @param $map
     * @param $page
     * @param $pageSize
     * @return array|bool|null
     */
    private function proInstanceEd($map,$page,$pageSize){
        $offset = ($page - 1)* $pageSize;
        $sql = "select * from process_instance ins 
                  LEFT JOIN instance_log log on ins.log_id = log.log_id
                  LEFT JOIN ea_role roles on roles.role_id = verify_role
                where end_time is not null 1=1 and {$map} ORDER BY create_time desc offset {$offset} limit {$pageSize}";
        $count = "select count(*) from process_instance ins 
                  LEFT JOIN instance_log log on ins.log_id = log.log_id
                where end_time is not null 1=1 and {$map}";
        $res = $this->_db->GetAll($sql);
        $total = $this->_db->GetOne($count);
        return DB::returnModelRes(['data'=>$res,'count'=>$total]);

    }

    public function getProInstanceIng($role,$keywords,$page,$pageSize){
        $map = " and pro_name = pro_name like '%{$keywords}%' ";
        if(!in_array($role,$this->adminRole)){
            $map .= " and  verify_role =  '{$role}' ";
        }
        return $this->proInstanceIng($map,$page,$pageSize);
    }

    public function getProInstanceEd($role,$keywords,$page,$pageSize){
        $map = " and pro_name = pro_name like '%{$keywords}%' ";
        if(!in_array($role,$this->adminRole)){
            $map .= " and  verify_role =  '{$role}' ";
        }
        return $this->proInstanceEd($map,$page,$pageSize);
    }

}
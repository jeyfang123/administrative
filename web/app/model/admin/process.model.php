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
        $sql = "insert into ".DB::TB_PRO_DETAIL."(pop_id,per_type,enter_type,exercise_basis,acc_conditions,fee,term,contact,supervise_tel)
                values(?,?,?,?,?,?,?,?,?)";
        $res = $this->_db->Execute($sql,[$proId,$process['perType'],$process['enterType'],$process['exerciseBasis'],$process['accConditions'],
            $process['fee'],$process['term'],$process['contact'],$process['supervise']]);
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

}
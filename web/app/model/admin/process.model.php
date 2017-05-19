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
//        echo $this->_db->errorMsg();
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
//        echo $this->_db->errorMsg();
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
//        echo $this->_db->errorMsg();
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
//        echo $this->_db->errorMsg();
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
     * 获取待发起事项（无差异）
     * @param $insId
     * @param $page
     * @param $pageSize
     * @return array|bool|null
     */
    public function getProUnInstance($insId,$page,$pageSize){
        $map = '';
        if(!empty($insId)){
            $map = " and pro_ins_id = '{$insId}' ";
        }
        $offset = ($page - 1)* $pageSize;
        $sql = "select * from process_instance ins left JOIN(
                    select rolename,flow.pro_id,role_id from (
                        select * from (select * ,row_number() over (PARTITION by pro_id ORDER BY flow_id asc ) as row_index from process_flow) innerflow where row_index =1) flow 
                    left JOIN process_node node on flow.last_nodeid = node.node_id
                LEFT JOIN ea_role on node.role = role_id) roles on ins.pro_id = roles.pro_id 
                LEFT JOIN ea_user on ins.create_userid = ea_user.id
                where 1=1 and status = '0' {$map} ORDER BY create_time desc offset {$offset} limit {$pageSize}";
        $count = "select count(*) from process_instance ins left JOIN(
                    select rolename,flow.pro_id from (
                        select * from (select * ,row_number() over (PARTITION by pro_id ORDER BY flow_id asc ) as row_index from process_flow) innerflow where row_index =1) flow 
                    left JOIN process_node node on flow.last_nodeid = node.node_id
                LEFT JOIN ea_role on node.role = role_id) roles on ins.pro_id = roles.pro_id 
                where 1=1 and status = '0' {$map}";
        $res = $this->_db->GetAll($sql);
//        echo $this->_db->errorMsg();
        $total = $this->_db->GetOne($count);
        return DB::returnModelRes(['data'=>$res,'count'=>$total])[0];
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
                  LEFT JOIN ea_user on ins.create_userid = ea_user.id
                where status = '1' {$map} ORDER BY ins.create_time desc offset {$offset} limit {$pageSize}";
        $count = "select count(*) from process_instance ins 
                    LEFT JOIN instance_log log on ins.log_id = log.log_id
                  where status = '1' {$map}";
        $res = $this->_db->GetAll($sql);
        $total = $this->_db->GetOne($count);
        return DB::returnModelRes(['data'=>$res,'count'=>$total])[0];
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
                  LEFT JOIN ea_user on ins.create_userid = ea_user.id
                where end_time is not null {$map} ORDER BY ins.create_time desc offset {$offset} limit {$pageSize}";
        $count = "select count(*) from process_instance ins 
                  LEFT JOIN instance_log log on ins.log_id = log.log_id
                where end_time is not null 1=1 {$map}";
        $res = $this->_db->GetAll($sql);
        $total = $this->_db->GetOne($count);
        return DB::returnModelRes(['data'=>$res,'count'=>$total])[0];
    }

    /**
     * 获取进行中事项（差异化）
     * @param $role
     * @param $keywords
     * @param $page
     * @param $pageSize
     * @return array|bool|null
     */
    public function getProInstanceIng($role =null,$keywords =null,$page,$pageSize){
        $map = " and pro_name = pro_name like '%{$keywords}%' ";
        $map = '';
        return $this->proInstanceIng($map,$page,$pageSize);
        if(!in_array($role,$this->adminRole)){
            $map .= " and  verify_role =  '{$role}' ";
        }
    }

    /**
     * 获取结束事项（无差异）
     * @param $role
     * @param $keywords
     * @param $page
     * @param $pageSize
     * @return array|bool|null
     */
    public function getProInstanceEd($role,$keywords,$page,$pageSize){
        $map = " and pro_name = pro_name like '%{$keywords}%' ";
        $map = '';
        return $this->proInstanceEd($map,$page,$pageSize);
        if(!in_array($role,$this->adminRole)){
            $map .= " and  verify_role =  '{$role}' ";
        }
    }

    /**
     * 获取所需材料
     * @param $proId
     * @return mixed
     */
    public function getMaterial($proId){
        $sql = " select * from process_node where pro_id = ? ";
        $res = $this->_db->GetAll($sql,[$proId]);
        return DB::returnModelRes($res)[0];
    }

    /**
     * 检查状态
     * @param $insId
     * @return mixed
     */
    public function checkAccept($insId){
        $sql = "select status from process_instance where pro_ins_id = ?";
        $res = $this->_db->GetOne($sql,[$insId]);
        return $res;
    }

    /**
     * 受理事项
     * @param $insId
     * @return string
     */
    public function acceptPro($insId,$material){
        $firstRoleSql = "select role_id,flow_id from process_instance LEFT JOIN (
                            select rolename,flow.pro_id,role_id,flow.flow_id from (
                              select * from (select * ,row_number() over (PARTITION by pro_id ORDER BY flow_id asc ) as row_index from process_flow) innerflow where row_index =1) flow 
                            left JOIN process_node node on flow.last_nodeid = node.node_id
                          LEFT JOIN ea_role on node.role = role_id ) roles on roles.pro_id = process_instance.pro_id where pro_ins_id = '{$insId}' ";

        //审批角色
        $firstRoleRes = $this->_db->GetRow($firstRoleSql);
//        echo $this->_db->errorMsg();
        $role = $firstRoleRes['role_id'];
        $flowId = $firstRoleRes['flow_id'];
        $users = Box::getObject('department','model','admin')->getDepartmentUsers($role);
        if($users != false){
            $index = rand(0,count($users)-1);
            //随即选取用户
            $userId = $users[$index]['depart_user_id'];
        }
        else{
            return 'noUser';
        }
        $insRes = $this->getInsInfo($insId);
        $insInfo = [
            'proId'=>$insRes['pro_id'],
            'createUser'=>$insRes['create_userid'],
            'createTime'=>$insRes['create_time'],
            'insId'=>$insRes['pro_ins_id']
        ];
        $logId = $this->planUserToPro($role,$userId,$insInfo,$flowId);
        $insSql = " update process_instance set upload_material = ?,status =? ,log_id = ? where pro_ins_id = ? ";
        $updateRes = $this->_db->Execute($insSql,[$material,'1',$logId,$insId]);
//        echo $this->_db->errorMsg();
        if($updateRes){
            $this->updateHeatpoint($insInfo['proId']);
            return [
                'role'=>$role,
                'user'=>$userId
            ];
        }
        else{
            return false;
        }
    }

    /**
     * 获取实例信息
     * @param $insId
     * @return mixed
     */
    function getInsInfo($insId){
        $sql = "select * from process_instance where pro_ins_id = ?";
        $res = $this->_db->GetRow($sql,[$insId]);
        return $res;
    }

    /**
     * 写入审批前记录
     * @param $insInfo proId,createUser,createTime,insId
     * @param $role
     * @param $user
     * @param $flowId
     * @return string $res
     */
    function planUserToPro($role,$user,$insInfo,$flowId){
        $sql = "insert into instance_log(pro_id,create_userid,create_time,verify_role,verify_userid,instance_id,flow_id) 
                values(?,?,?,?,?,?,?) returning log_id";
        $res = $this->_db->GetOne($sql,[$insInfo['proId'],$insInfo['createUser'],$insInfo['createTime'],$role,$user,$insInfo['insId'],$flowId]);
        $this->updateRoleAccept($role);
//        echo $this->_db->errorMsg();
        return $res;
    }

    /**
     * 写入审批结果，并更新下一处理部门
     * @param $status
     * @param $desc
     * @param $next
     * @param $insInfo insId
     */
    function examPro($status,$desc,$next,$insInfo){
        $date = date('Y-m-d H:i:s');
        $sql = " update instance_log set verify_time = ?,verify_status=?,verify_desc = ?,next_veri_role = ? where log_id = ? ";
        $res = $this->_db->Execute($sql,[$date,$status,$desc,$next,$insInfo['insId']]);
        return $res;
    }

    /**
     * 已知当前审批角色，获取下一审批部门
     * @param $proId
     * @param $role
     * @return mixed
     */
    function getLasted($proId,$role){
        $sql = "select * from process_node where node_id = (
                    select next_nodeid from process_flow where pro_id = ? and last_nodeid = 
                    (select node_id from process_node where pro_id = ? and role = ?))";
        $res = $this->_db->GetRow($sql,[$proId,$proId,$role]);
        return $res;
    }

    /**
     * 更新部门收到事件数量
     * @param $role
     * @return mixed
     */
    function updateRoleAccept($role){
        $sql = "update ea_role set total_accept = total_accept + 1 where role_id = ?";
        $res = $this->_db->Execute($sql,[$role]);
        return $res;
    }

    /**
     * 更新事项热度
     * @param $proId
     * @return mixed
     */
    function updateHeatpoint($proId){
        $sql = "update process_detail set heatpoint = heatpoint + 1 where pop_id = ?";
        $res = $this->_db->Execute($sql,[$proId]);
        return $res;
    }

    /**
     * 获取个人待审批事件
     * @param $userId
     * @param null $insId
     * @return mixed
     */
    function getOwnPending($userId,$insId = null){
        $map = " verify_userid = '{$userId}' and verify_time is null ";
        if(!empty($insId)){
            $map .= " and pro_ins_id = '{$insId}' ";
        }
        $sql = "select ins.pro_id,ins.pro_ins_id,pro_name,COALESCE(compellation,artificial) as username,
                case WHEN valued = '1' then '个人' when valued = '2' then '企业' end as usertype,
                ins.create_time from process_instance ins 
                LEFT JOIN instance_log logs on ins.log_id = logs.log_id
                LEFT JOIN ea_user users on users.\"id\" = ins.create_userid
                where {$map} ";
        $res = $this->_db->GetAll($sql);
        return $res;
    }

    function getPendingCount($userId,$role){
        if($role == '094611aa-bbe4-47f3-b3c2-60692927f095'){
            $sql = "select count(*) from process_instance ins left JOIN(
                    select rolename,flow.pro_id from (
                        select * from (select * ,row_number() over (PARTITION by pro_id ORDER BY flow_id asc ) as row_index from process_flow) innerflow where row_index =1) flow 
                    left JOIN process_node node on flow.last_nodeid = node.node_id
                LEFT JOIN ea_role on node.role = role_id) roles on ins.pro_id = roles.pro_id 
                where 1=1 and status = '0' ";
            $total = $this->_db->GetOne($sql);
        }
        else{
            $sql = "select count(*) from process_instance ins 
                LEFT JOIN instance_log logs on ins.log_id = logs.log_id
                LEFT JOIN ea_user users on users.\"id\" = ins.create_userid
                where verify_userid = '{$userId}' and verify_time is null  ";
            $total = $this->_db->GetOne($sql);
        }
        return $total;
    }

    /**
     * 获取事项实例用户
     * @param $insId
     * @return mixed
     */
    function getProUser($insId){
        $sql = "select * from process_instance ins 
                LEFT JOIN process_detail on pop_id = ins.pro_id
                LEFT JOIN ea_user users on users.id = ins.create_userid where pro_ins_id = '{$insId}'";
        $res = $this->_db->GetRow($sql);
        return $res;
    }

    /**
     * 获取当前节点所需材料
     * @param $proId
     * @param $role
     * @return mixed
     */
    function getCurNodeMaterial($proId,$role){
        $sql = "select material from process_node where pro_id = '{$proId}' and role = '{$role}'";
//        var_dump($sql);
        $res = $this->_db->GetOne($sql);
        return $res;
    }

    /**
     * 审核拒绝
     * @param $denyMsg
     * @param $logId
     * @param $insId
     * @return bool
     */
    function denyPro($denyMsg,$logId,$insId,$logId){
        $date = date('Y-m-d H:i:s');
        $updateLogsql = "update instance_log set verify_status = '40',verify_time = ? , verify_desc = ? where log_id = ?";
        $updateLog = $this->_db->Execute($updateLogsql,[$date,$denyMsg,$logId]);
        $updateInsSql = "update process_instance set status = '3', end_time = ? where pro_ins_id = ?";
        $upadteIns = $this->_db->Execute($updateInsSql,[$date,$insId]);
        if($updateLog === false || $upadteIns === false){
            return false;
        }
        return true;
    }

    function agreePro($proId,$role,$logId,$insId){
        $nextRoleRes = $this->getLasted($proId,$role);
        if($nextRoleRes === false){
            return false;
        }
        //审核结束
        else if($nextRoleRes == null){
            $date = date('Y-m-d H:i:s');
            $updateLogsql = "update instance_log set verify_status = '20',verify_time = ? where log_id = ?";
            $updateLog = $this->_db->Execute($updateLogsql,[$date,$logId]);
            $updateInsSql = "update process_instance set status = '2', end_time = ? where pro_ins_id = ?";
            $upadteIns = $this->_db->Execute($updateInsSql,[$date,$insId]);
            if($updateLog == false || $upadteIns == false){
                return false;
            }
            else{
                return true;
            }
        }
        //继续执行
        else{
            $nextRole = $nextRoleRes['role'];
            //插入新待审批记录
            $flowsql = "select * from process_flow where pro_id = ? and last_nodeid = ? ";
            $flowRes = $this->_db->GetRow($flowsql,[$proId,$nextRoleRes['node_id']]);
            $flowId = $flowRes['flow_id'];
            $users = Box::getObject('department','model','admin')->getDepartmentUsers($nextRole);
            if($users != false){
                $index = rand(0,count($users)-1);
                //随即选取用户
                $userId = $users[$index]['depart_user_id'];
            }
            else{
                return 'noUser';
            }
            $insRes = $this->getInsInfo($insId);
            $insInfo = [
                'proId'=>$insRes['pro_id'],
                'createUser'=>$insRes['create_userid'],
                'createTime'=>$insRes['create_time'],
                'insId'=>$insRes['pro_ins_id']
            ];
            $newLogId = $this->planUserToPro($nextRole,$userId,$insInfo,$flowId);
            $updateLogsql = "update instance_log set verify_status = '20',verify_time = ?,next_veri_role = ? where log_id = ?";
            $date = date('Y-m-d H:i:s');
            $updateLog = $this->_db->Execute($updateLogsql,[$date,$nextRole,$logId]);
            $insSql = " update process_instance set log_id = ? where pro_ins_id = ? ";
            $updateRes = $this->_db->Execute($insSql,[$newLogId,$insId]);
            if($updateRes === false){
                return false;
            }
            //发送信息
            $time = date('H:i:s');
            $messageObj = Box::getObject('message','controller','public');
            $toUser = $userId;
            $info = $this->getProUser($insId);
            $content = [
                'proName'=>$info['proName'],
                'fromUser'=>($info['compellation'] ? $info['compellation'] : $info['artificial']),
                'time'=>$time
            ];
            $messageObj->systemToWorker($toUser,$content);

            return true;
        }
    }

    /**
     * 更新部门办结数目
     * @param $role
     */
    function updateRoleFinished($role){
        $sql = "update ea_role set finished = finished +1 where role_id = ?";
        $this->_db->Execute($sql,[$role]);
    }

    //统计

    /**
     * 总发起数
     * @return mixed
     */
    function totalInstance(){
        $sql = "select count(*) from process_instance";
        $res = $this->_db->GetOne($sql);
        return $res;
    }

    /**
     * 总受理数
     * @return mixed
     */
    function totalAccept(){
        $sql = "select count(*) from process_instance where status != '0'";
        $res = $this->_db->GetOne($sql);
        return $res;
    }

    /**
     * 总办结数
     * @return mixed
     */
    function totalFinished(){
        $sql = "select count(*) from process_instance where status = '2' or status = '3' ";
        $res = $this->_db->GetOne($sql);
        return $res;
    }

    /**
     * 总待办数
     * @return mixed
     */
    function totalUnFinished(){
        $sql = "select count(*) from process_instance where status = '1' ";
        $res = $this->_db->GetOne($sql);
        return $res;
    }

    function lastPeriodSta(){
        $sql = "select lastdate,COALESCE(instable.total,0) count from(
                    SELECT
                    regexp_split_to_table(to_char(
                            CURRENT_DATE,
                            'YYYY-MM-DD'
                        ) || ',' ||	to_char(
                            CURRENT_DATE - INTERVAL '1 day',
                            'YYYY-MM-DD'
                        ) || ',' || to_char(
                            CURRENT_DATE - INTERVAL '2 day',
                            'YYYY-MM-DD'
                        ) || ',' || to_char(
                            CURRENT_DATE - INTERVAL '3 day',
                            'YYYY-MM-DD'
                        ) || ',' || to_char(
                            CURRENT_DATE - INTERVAL '4 day',
                            'YYYY-MM-DD'
                        ) || ',' || to_char(
                            CURRENT_DATE - INTERVAL '5 day',
                            'YYYY-MM-DD'
                        ) || ',' || to_char(
                            CURRENT_DATE - INTERVAL '6 day',
                            'YYYY-MM-DD'
                        ), ',' ) as lastdate) perior
                    LEFT JOIN 
                    (select count(*) as total,to_char(ins.create_time,'YYYY-MM-DD') as createtime from process_instance ins GROUP BY
                    to_char( ins.create_time,'YYYY-MM-DD')) instable on instable.createtime = perior.lastdate
                    ORDER BY perior.lastdate asc";
        $res = $this->_db->GetAll($sql);
        return $res;
    }

    /**
     * 部门工作统计
     * @return mixed
     */
    function departSta(){
        $sql = "select accept.rolename,total_accept,finished from 
                  (select rolename,total_accept,role_id from ea_role where role_id != '664e38e7-3a58-4bf2-9b6a-60614f105bb7'
                    and role_id != 'c6a919ac-0191-46f2-b938-84a387596ec8'
                    and role_id != '094611aa-bbe4-47f3-b3c2-60692927f095'
                    and role_id != 'c6b813da-2ef4-439d-a8b1-9ae019352ff1') accept
                LEFT JOIN(
                    select ea_role.rolename,ea_role.role_id,COALESCE(finished.count,0) finished from ea_role
                    LEFT JOIN
                    (select verify_role,count(*) count,rolename from instance_log ins
                    LEFT JOIN ea_role roles on ins.verify_role = roles.role_id
                    where ins.verify_time is not null
                    GROUP BY ins.verify_role,rolename) finished
                on ea_role.role_id = finished.verify_role
                where role_id != '664e38e7-3a58-4bf2-9b6a-60614f105bb7'
                and role_id != 'c6a919ac-0191-46f2-b938-84a387596ec8'
                and role_id != '094611aa-bbe4-47f3-b3c2-60692927f095'
                and role_id != 'c6b813da-2ef4-439d-a8b1-9ae019352ff1') finished
                on finished.role_id = accept.role_id";
        $res = $this->_db->GetAll($sql);
        return $res;
    }



}
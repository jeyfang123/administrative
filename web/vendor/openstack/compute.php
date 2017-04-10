<?php
class Compute extends OpenStack
{
    function getServersDetail($projectid){
        return $this->_req->get("/v2/$projectid/servers/detail", [], []);
    }

    function getServerDetail($projectid, $serverid){
        return $this->_req->get("/v2/$projectid/servers/$serverid", [], []);        
    }

    function getConsoleOutput($projectid, $serverid, $size = 100){
        $body = [
            "os-getConsoleOutput" => [
                "length" => $size
            ]
        ];

        $url = "/v2/$projectid/servers/$serverid/action";
        return $this->_req->post($url, [], $body);
    }

    function getVncConsole($projectid, $serverid){
        $body = [
                "os-getVNCConsole" => [
                    "type" => "novnc"
                ]
            ];
        $url = "/v2/$projectid/servers/$serverid/action";
        return $this->_req->post($url, [], $body);
    }

    function createServer($projectid, $name, $image, $flavor, 
                            $nid, $portid, $keypair = '', $metadata = []){
        $body = [
                "server" => [
                    "name" => "$name",
                    "imageRef" => "$image",
                    "flavorRef" => "$flavor",
                    "metadata" => $metadata,
                    "min_count" => "1",
                    "max_count" => "1",
                    "networks" => [[
                                    "uuid" => $nid,
                                    "port" => $portid,
                                ]]
                ]
            ];

        if($keypair != ''){
            $body['server']['key_name'] = $keypair;
        }
        return $this->_req->post("/v2/$projectid/servers", [], $body);
    }

    function updateServer($projectid, $serverid, $val){
        $body = ['server' => $val];

        return $this->_req->put("/v2/$projectid/servers/$serverid", [], $body);
    }

    function createFlavor($projectid, $name, $vcpu, $mem, $disk){
        $body = [
                "flavor" => [
                    "name" => $name,
                    "ram" => $mem,
                    "vcpus" => $vcpu,
                    "disk" => $disk,
                    // "id" => "auto",
                    // "rxtx_factor" => 2.0
                    "os-flavor-access:is_public" => 'false'
                ]
            ];

        $url = "/v2/$projectid/flavors";
        $res = $this->_req->post($url, [], $body);
        return $res;        
    }

    function getFlavorDetail($projectid, $flavorid){
        $url = "/v2/$projectid/flavors/$flavorid";
        $res = $this->_req->get($url, [], $body);
        return $res;

    }

    function getImageDetail($projectid, $imageid){
        $url = "/v2/$projectid/images/$imageid";
        $res = $this->_req->get($url, [], $body);
        return $res;
    }

    function createKeypair($projectid, $name){
        $url = '/v2/' . $projectid . '/os-keypairs';
        $body = [
            'keypair' => [
                'name' => $name,
            ]
        ];

        return $this->_req->post($url, [], $body);
    }

    function showKeypair($projectid, $name){
        $url = '/v2/' . $projectid . '/os-keypairs/' . $name;

        return $this->_req->get($url, [], []);
    }

    function deleteKeypair($projectid, $name){
        $url = '/v2/' . $projectid . '/os-keypairs/' . $name;

        return $this->_req->delete($url, [], []);
    }

    /**
     * [lock the instance]
     * @param  [string] $projectid  [tanent_id]
     * @param  [string] $instanceid [server_id]
     * @return [array]              [description]
     */
    function lockInstance($projectid, $instanceid) {
        $url = '/v2/' . $projectid . '/servers/' . $instanceid . '/action';

        $body = [
            'lock' => ''
        ];

        return $this->_req->post($url, [], $body);
    }

    /**
     * [unlocak the instance]
     * @param  [string] $projectid  [tanent_id]
     * @param  [string] $instanceid [server_id]
     * @return [array]              [description]
     */
    function unlockInstance($projectid, $instanceid) {
        $url = '/v2/' . $projectid . '/servers/' . $instanceid . '/action';
        
        $body = [
            'unlock' => null
        ];

        return $this->_req->post($url, [], $body);
    }

    /**
     * [pause the instance]
     * @param  [string] $projectid  [tanent_id]
     * @param  [string] $instanceid [server_id]
     * @return [array]              [description]
     */
    function pauseInstance($projectid, $instanceid) {
        $url = '/v2/' . $projectid . '/servers/' . $instanceid . '/action';

        $body = [
            'pause' => null
        ];

        return $this->_req->post($url, [], $body);
    }

    /**
     * [resume the instance]
     * @param  [string] $projectid  [tanent_id]
     * @param  [string] $instanceid [server_id]
     * @return [array]             [description]
     */
    function resumeInstance($projectid, $instanceid) {
        $url = '/v2/' . $projectid . '/servers/' . $instanceid . '/action';

        $body = [
            'unpause' => null
        ];

        return $this->_req->post($url, [], $body);
    }

    /**
     * [rebuild the instance]
     * @param  [string] $projectid  [tanent_id]
     * @param  [string] $instanceid [server_id]
     * @param  [array]  $serverInfo [serverInfo]
     * @return [array]              [description]
     */
    function rebuildInstance($projectid, $instanceid, $serverInfo) {
        $url = '/v2/' . $projectid . '/servers/' . $instanceid . '/action';

        $body = [
            'rebuild' => [
                'imageRef' => $serverInfo['imageRef'],
                'name' => $serverInfo['hostname'],
                'adminPass' => $serverInfo['pwd']
            ]
        ];

        return $this->_req->post($url, [], $body);
    }

    /**
     * [delete the instance]
     * @param  [string] $projectid  [tanent_id]
     * @param  [string] $instanceid [server_id]
     * @return [array]              [description]
     */
    function deleteInstance($projectid, $instanceid) {
        $url = "/v2/$projectid/servers/$instanceid";

        return $this->_req->delete($url, [], []);
    }

    /**
     * [stop the instance]
     * @param  [string] $projectid  [tanent_id]
     * @param  [string] $instanceid [server_id]
     * @return [array]              [description]
     */
    function stopInstance($projectid, $instanceid) {
        $url = '/v2/' . $projectid . '/servers/' . $instanceid . '/action';

        $body = [
            'os-stop' => null
        ];

        return $this->_req->post($url, [], $body);
    }

    /**
     * [start the instance]
     * @param  [string] $projectid  [tanent_id]
     * @param  [string] $instanceid [server_id]
     * @return [array]              [description]
     */
    function startInstance($projectid, $instanceid) {
        $url = '/v2/' . $projectid . '/servers/' . $instanceid . '/action';

        $body = [
            'os-start' => null
        ];

        return $this->_req->post($url, [], $body);
    }

    /**
     * [reboot the instance]
     * @param  [string] $projectid  [tanent_id]
     * @param  [string] $instanceid [server_id]
     * @param  [mixed]  $type       [hard reboot or reboot]
     * @return [array]              [description]
     */
    function rebootInstance($projectid, $instanceid, $type) {
        $url = '/v2/' . $projectid . '/servers/' . $instanceid . '/action';

        $body = [
            'reboot' => [
                'type' => $type
            ]
        ];

        return $this->_req->post($url, [], $body);
    }

    /**
     * [根据tenant_id去列出主机的信息]
     * @param  [string] $projectid [tenant_id]
     * @return [type]            [description]
     */
    function listHost($projectid) {
        $url = '/v2/' . $projectid . '/os-hosts';
        $res= $this->_req->get($url, [], []);

        return  $res;
        
    }

    function getHostInfo($projectid, $hostname) {
        $url = '/v2/' . $projectid . '/os-hosts/' . $hostname;

        return $this->_req->get($url, [], []);
    }

    function getUsage($projectid) {
        $url = '/v2/' . $projectid . '/os-simple-tenant-usage/' . $projectid;

        return $this->_req->get($url, [], []);
    }

    function hostSet($projectid) {
        $url = '/v2/' . $projectid . '/os-services';

        return $this->_req->get($url, [], []);
    }

    function listUsage($projectid,$start,$end) {
      $url = '/v2/' . $projectid . '/os-simple-tenant-usage?start='.$start.'T00:00:00&end='.$end.'T00:00:00&detailed=1';
        return $this->_req->get($url, [], []);
    }


    function listVolume($projectid) {
        // $url = '/v2/' . $projectid . '/os-volumes/detail';
        $url = '/v2/' . $projectid . '/os-volumes';

        return $this->_req->get($url, [], []);
    }

    function useLog($adminprojectid, $projectid) {
        $url = "/v2/​$adminprojectid/limits​/$adminprojectid?tenant_id=$projectid";

        return $this->_req->get($url, [], []);
    }

    function createSSHRule($projectid, $pid) {
        $url = '/v2/' . $projectid . '/os-security-group-rules';

        $body = [
            'security_group_rule' => [
                'ip_protocol' => 'tcp',
                'from_port' => '22',
                'cidr' => '0.0.0.0/0',
                'parent_group_id' => $pid,
                'to_port' => '65535'
            ]
        ];

        return $this->_req->post($url, [], $body);
    }

    function getParentRule($projectid) {
        $url = '/v2/' . $projectid . '/os-security-groups';

        return $this->_req->get($url, [], []);
    }

    function networkDetail($networkid) {
        //$url = '/v2/' . $projectid . '/os-networks/' . $networkid;
        $url ='/v2.0/networks/'.$networkid;

        return $this->_req->get($url, [], []);
    }

    function imageDetail($projectid, $networkid) {
        $url = '/v2/' . $projectid . '/images/' . $networkid;

        return $this->_req->get($url, [], []);
    }

    function resourceUse() {
        $url = '/v2/meters';

        return $this->_req->get($url, [], []);
    }

    function defaultMgm($projectid) {
        $url = '/v2/' . $projectid . '/os-quota-sets/' . $projectid . '/defaults';

        return $this->_req->get($url, [], []);
    }

    function attachVolume($projectid, $instanceid, $volumeid, $device = '/dev/vdb') {
        $url = '/v2/' . $projectid . '/servers/' . $instanceid . '/os-volume_attachments';

        $body = [
            'volumeAttachment' => [
                'volumeId' => $volumeid
            ]
        ];

        return $this->_req->post($url, [], $body);
    }
}
?>
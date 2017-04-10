<?php
class Network extends OpenStack
{
    function createSubnet($projectid, $nid, $cidr){
        $body = [
            "subnet" => [
                "network_id" => $nid,
                "ip_version" => 4,
                'tenant_id' => $projectid,
                "cidr" => $cidr
            ]
        ];

        return $this->_req->post("/v2.0/subnets", [], $body);
    }

    function removeSubnet($subnetid) {
        $url = '/v2.0/subnets/' . $subnetid;

        return $this->_req->delete($url, [], []);
    }

    function createPort($nid){
        $body = [
            "port" => [
                "network_id" => $nid,
                "name" => "private-port",
                "admin_state_up" => true
            ]
        ];
        return $this->_req->post("/v2.0/ports", [], $body);
    }

    function createNetwork($name, $projectid){
        $body = [
            "network" => [
                "name" => $name,
                "admin_state_up" => true,
                "tenant_id" => $projectid
            ]
        ];

        return $this->_req->post("/v2.0/networks", [], $body);
    }

    function getNetwork() {
        $url = '/v2.0/networks';

        $res = $this->_req->get($url, [], []);

        $networkid = $res['result']['networks'][0]['id'];

        return ['network_id' => $networkid];
    }

    function listNetwork() {
        $url = '/v2.0/networks';

        return $this->_req->get($url, [], []);
    }

    function addRouterInterface($routerid, $subnetid) {
        $body = [
            'subnet_id' => $subnetid
        ];

        $url = '/v2.0/routers/' . $routerid . '/add_router_interface';

        return $this->_req->put($url, [], $body);
    }

    function deleteNetwork($networkid) {
        $url = '/v2.0/networks/' . $networkid;

        return $this->_req->delete($url, [], []);
    }

    function listPorts($networkid){
        $body=[
            'netword_id'=>$networkid
        ];
        $url='/v2.0/ports?network_id='.$networkid;
        return $this->_req->get($url,[],[]);
    }

    function deletePort($port) {
        $url = '/v2.0/ports/' . $port;

        return $this->_req->delete($url, [], []);
    }

    function networkDetail($networkid) {
        $url = '/v2.0/networks/' . $networkid;

        return $this->_req->get($url, [], []);
    }
}
?>
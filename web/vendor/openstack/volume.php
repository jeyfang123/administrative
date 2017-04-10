<?php
class Volume extends OpenStack
{
    function create($projectid, $size, $name, $desc){
        $sizeval = intval($size);
        $body = [
            'volume' => [
                'status' => 'creating',
                'attach_status' => 'detached',
                'size' => $sizeval,
                'description' => $desc,
                "name" => $name,
            ]
        ];

        $url = '/v2/'.$projectid.'/volumes';

        $res = $this->_req->post($url, [], $body);
        return $res;
    }

    function createVolumeFromImage($imageid, $projectid, $size, $name, $desc) {
        $sizeval = intval($size);
        $body = [
            'volume' => [
                'status' => 'creating',
                'attach_status' => 'detached',
                'size' => $sizeval,
                'description' => $desc,
                'name' => $name,
                'imageRef' => $imageid
            ]
        ];
        
        $url = '/v2/'.$projectid.'/volumes';

        $res = $this->_req->post($url, [], $body);
        return $res;
    }

    function remove($projectid, $volumeid){
        $url = '/v2/'.$projectid.'/volumes/' . $volumeid;
        $res = $this->_req->delete($url, [], $body);
        return $res;        
    }

    function attach($projectid, $volumeid, $instanceid, $instancename = '', $mountpoint = '/dev/vdc'){
        $body = [
            'os-attach' => [
                'instance_uuid' => $instanceid,
                'host_name' => $instancename,
                'mountpoint' => $mountpoint,
            ]
        ];

        $url = '/v2/'.$projectid.'/volumes/' . $volumeid . '/action';
        $res = $this->_req->post($url, [], $body);
        return $res;            
    }

    function resetStatus($projectid, $volumeid){
        $body = [
            'os-reset_status' => [
                'status' => 'available',
                'attach_status' => 'detached',
                'migration_status' => 'migrating',
            ]
        ];

        $url = '/v2/'.$projectid.'/volumes/' . $volumeid . '/action';
        $res = $this->_req->post($url, [], $body);
        return $res;
    }

    function getVolume($projectid) {
        $url = '/v2/' . $projectid . '/volumes/detail';

        return $this->_req->get($url, [], []);
    }

    function listSnapShot($projectid) {
        $url = '/v2/' . $projectid . '/snapshots/detail';

        return $this->_req->get($url, [], []);
    }

    function createSnapShot($projectid, $volumeid, $username) {
        $url = "/v2/$projectid/snapshots";

        $snapshotName = 'snapshot_for_' . $username;
        $description  = 'snapshot_in_' . date('Y-m-d H:i', time());

        $body = [
            'snapshot' => [
                'name' => $snapshotName,
                'description' => $description,
                'volume_id' => $volumeid,
                'force' => true
            ]
        ];

        return $this->_req->post($url, [], $body);
    }

    function removeSnapShot($projectid, $snapshotid) {
        $url = "/v2/$projectid/snapshots/$snapshotid";

        return $this->_req->delete($url, [], []);
    }

    function listVolume($projectid) {
        $url = '/v1/' . $projectid . '/volumes';

        return $this->_req->get($url, [], []);
    }
}
?>
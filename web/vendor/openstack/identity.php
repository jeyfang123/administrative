<?php
class Identity extends OpenStack
{
    function getToken($user, $pwd, $tenantName){
        $body = [
                "auth" => [
                    "tenantName" => "$tenantName",
                    "passwordCredentials" => [
                        "username" => "$user",
                        "password" => "$pwd"
                    ]
                ]
            ];

        $res = $this->_req->post('/v2.0/tokens', [], $body);

        return $res;
    }
    
    function auth($projectname, $token){
        $body = [
                "auth" => [
                    "tenantName" => $projectname,
                    "token" => [
                        "id" => $token,
                    ]
                ]
            ];

        $res = $this->_req->post('/v2.0/tokens', [], $body);

        return $res;        
    }
    
    function checkToken($token){
        $url = "/v2.0/tenants";

        return $this->_req->get($url, [], []);
    }

    function createUser($tenantid, $username, $passwd, $email){
        $body = [
                "user" => [
                    "tenantId" => "$tenantid",
                    "name" => "$username",
                    "email" => "$email",
                    "password" => "$passwd",
                    "enabled" => true,
                ]
            ];

        $res = $this->_req->post('/v2.0/users', [], $body);

        return $res;        
    }

    function createProject($projectname, $desc){
        $body = [
                "project" => [
                    "description" => "$desc",
                    "name" => "$projectname",
                ]
            ];

        $res = $this->_req->post('/v3/projects', [], $body);
        return $res;        
    }

    function removeProject($projectid) {
        $url = '/v3/projects/' . $projectid;

        return $this->_req->delete($url, [], []);
    }

    function removeUser($userid){
        $url = "/v2.0/users/$userid";
        $res = $this->_req->delete($url, [], []);
        return $res;
    }

    function listRoles($name){
        $url = '/v3/roles?name=' . $name;
        $res = $this->_req->get($url, [], []);

        return $res;        
    }

    function grantRolesToProject($projectid, $userid, $roleid){
        $url = '/v3/projects/' . $projectid . '/users/' . $userid . '/roles/' . $roleid;
        $res = $this->_req->put($url, [], []);

        return $res;
    }

    function listTenant() {
        $url = '/v2.0/tenants';

        return $this->_req->get($url, [], []);
    }

    function listRole() {
        $url = '/v3/roles';

        return $this->_req->get($url, [], []);
    }
    
    function getTokenUnscoped() {
        $url = '/v3/auth/tokens';

        $body = [
            'auth' => [
                'identity' => [
                    'methods' => ['password'],
                    'password' => [
                        'user' => '7416194925754edc92690ad668291c1b',
                        'password' => 'daemon.datatom'
                    ]
                ],
                'scope' => 'unscoped'
            ]
        ];

        return $this->_req->post($url, [], $body);
    }
}
?>
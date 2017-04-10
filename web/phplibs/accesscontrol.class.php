<?php
class AccessControl
{
    public static function hasAdminPermission() {
        $danaclient = new DanaClient(Session::get('access_token'));
        $result = $danaclient->permission()->check(PERMISSION_ADMIN);
        if ($result['code'] == CGI_SUCCESS) {
            return true;
        }
        return false;
    }

    public static function isAdmin() {
        $userinfo = Session::getLander();
        if($userinfo['groupname'] === 'system'){
            return true;
        }
        return false;
    }

    public static function hasLogin() {
        $logininfo = Session::getLander();
        if ($logininfo != '') {
            if ($logininfo['name'] != "" && $logininfo['token'] != "")
                return true;
        }
        return false;
    }

    public static function login($username, $password) {
        $dana = new DanaClient();
        $result = $dana->login($username, $password);
        if ($result['code'] != CGI_SUCCESS) {
            return ['code' => 300];
        }
        $token = $result['result']['access_token'];
        setcookie("access_token", $token, time() + 3600 * 24, '/');
        Session::set ("access_token", $token);

        $result = $dana->user()->getUserByName($username);
        $roleids = '';
        $groupname = '';
        $uid = '';
        $nickname = '';
        if ($result['code'] == CGI_SUCCESS) {
            $userinfo = $result['result'];
            $uid = $userinfo['userid'];
            $nickname = $userinfo['nickname'];
            $roleidArr = [];
            foreach($userinfo['roleids'] as $role){
                $roleidArr[] = $role['roleid'];
            }
            $roleids = implode(',', $roleidArr);
            $groupname = $userinfo['groupname'];
            $groupid = $userinfo['groupid'];
        }
        $result = $dana->role()->getPermissionById($roleids);
        $permission = '';
        if ($result['code'] == CGI_SUCCESS) {
            $permission = $result['result']['permission'];
        }
        $lander = [
            'name' => $username,
            'groupname' => $groupname,
            'uid' => $uid,
            'token' => $token,
            'groupid' => $groupid,
            'nickname' => $nickname,
            'permission' => $permission
        ];
        Session::setLander($lander, $token);
        $result = $dana->license()->get();
        if($result['code'] == CGI_SUCCESS){
            $result = Session::setLicense($result['result'], $token);
        }
        return ['code' => 200, 'token' => $token];
    }

    public static function cookieLogin($username, $password) {
        $dana = new DanaClient();
        $result = $dana->login($username, $password);
        if ($result['code'] != CGI_SUCCESS) {
            return ['code' => 300];
        }
        $token = $result['result']['access_token'];
        setcookie($username."_token", $token, 0, '/');
        return ['code' => 200, 'token' => $token];
    }
    public static function hasCookieLogin($username) {
        if (isset ($_COOKIE[$username."_token"]))
            return true;
        return false;
    }
    public static function cookieLogout($username) {
        setcookie($username."_token", "", -1, '/');
        return true;
    }

    public static function logout() {
        Session::newDestroy();
    }

    public static function setTimeout() {

    }

    public static function getLoginUserInfo() {
        $logininfo = Session::getLander();
        return $logininfo;
    }
}
?>

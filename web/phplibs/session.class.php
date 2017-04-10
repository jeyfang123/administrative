<?php
Class Session
{
    public static function get($key) {
        session_start();
        $value = "";
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
        }
        session_write_close();
        return $value;
    }

    public static function set($key, $value) {
        session_start();
        $_SESSION[$key] = $value;
        session_write_close();
    }

    public static function destroy($key) {
        session_start();
        unset($_SESSION[$key]);
        session_write_close();
    }

    public static function newDestroy($key){
        $danaclient = new DanaClient(self::getId());
        $result = $danaclient->session()->destroy();
        setcookie("access_token", "", time() - 3600 * 24, '/');
    }

    public static function create(){
        $danaclient = new DanaClient(self::getId());
        $result = $danaclient->session()->create();
    }

    public static function getId(){
        return $_COOKIE["access_token"];
    }

    public static function getUsershare($session_id = '') {
        $key = 'usershare';
        if($session_id == ''){
            $session_id = Session::get('access_token');
        }
        $danaclient = new DanaClient($session_id);
        $result = $danaclient->session()->get($key, $session_id);
        if ($result['code'] == 200) {
            return $result['result'][$key];
        } else {
            return '';
        }
    }

    public static function setUsershare($value, $session_id = '') {
        $key = 'usershare';
        $result = $danaclient->session()->set($key, $value, $session_id);
        return $result;
    }

    public static function getLander() {
        $key = 'lander';
        $danaclient = new DanaClient(self::getId());
        $result = $danaclient->session()->get($key);
        if ($result['code'] == 200) {
            $lander = $result['result'][$key];
/*            if(isset($lander['nickname'])){
                $lander['nickname'] = urldecode($lander['nickname']);
            }
*/            return $lander;
        } else {
            return [];
        }
    }

    public static function getLanderId() {
        $lander = self::getLander();
        if (isset($lander['uid'])) {
            return $lander['uid'];
        } else {
            return '';
        }
    }

    public static function getLanderName() {
        $lander = self::getLander();
        if (isset($lander['name'])) {
            return $lander['name'];
        } else {
            return '';
        }
    }

    public static function getLanderPermission() {
        $lander = self::getLander();
        if (isset($lander['permission'])) {
            return $lander['permission'];
        } else {
            return '';
        }
    }

    public static function setLander($value, $session_id = '') {
        $key = 'lander';
        if($session_id == ''){
            $session_id = self::getId();
        }
        $dana = new DanaClient($session_id);
        $result = $dana->session()->set($key, $value);
        return $result;
    }

    public static function getLicense() {
        $key = 'license';
        $session_id = self::getId();
        $danaclient = new DanaClient($session_id);
        $result = $danaclient->session()->get($key);
        if ($result['code'] == 200) {
            return $result['result'][$key];
        } else {
            return '';
        }
    }

    public static function setLicense($value, $session_id = '') {
        $key = 'license';
        if($session_id == ''){
            $session_id = self::getId();
        }
        $dana = new DanaClient($session_id);
        $result = $dana->session()->set($key, $value);
        return $result;
    }

    public static function refresh($key){
        $dana = new DanaClient(self::getId());
        $result = $dana->session()->refresh();
    }
}
?>

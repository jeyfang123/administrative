<?php
class UserConfig
{
    static $ssdb = null;
    const SSDB_USER_CONFIG_NAME = '__DATRIX_::USER_::CONFIG__';
    const DEFAULT_PUBDIR_KEY    = 'default_pubdir';
    const USER_PUBDIR_KEY_PREFIX    = 'user_::default_pubdir::';
    const GROUP_PUBDIR_KEY_PREFIX    = 'group_::default_pubdir::';

    function __construct() {
        if($this->ssdb === null){
            $this->ssdb = new SSDBClient();
        }
    }

    public function setDefaultPubdir($pubdirid){
        return $this->ssdb->hset(self::SSDB_USER_CONFIG_NAME, self::DEFAULT_PUBDIR_KEY, $pubdirid);
    }

    public function getDefaultPubdir(){
        return $this->ssdb->hget(self::SSDB_USER_CONFIG_NAME, self::DEFAULT_PUBDIR_KEY);
    }

    public function setUserDefaultPubdir($userid, $pubdirid){
        if($userid == ''){
            return false;
        }
        $key = self::USER_PUBDIR_KEY_PREFIX . $userid;
        return $this->ssdb->hset(self::SSDB_USER_CONFIG_NAME, $key, $pubdirid);
    }

    public function getUserDefaultPubdir($userid){
        if($userid == ''){
            return false;
        }
        $key = self::USER_PUBDIR_KEY_PREFIX . $userid;        
        return $this->ssdb->hget(self::SSDB_USER_CONFIG_NAME, $key);
    }

    public function setGroupDefaultPubdir($groupid, $pubdir){
        if($groupid == ''){
            return false;
        }
        $key = self::GROUP_PUBDIR_KEY_PREFIX . $groupid;
        return $this->ssdb->hset(self::SSDB_USER_CONFIG_NAME, $key, $pubdir);        
    }

    public function getGroupDefaultPubdir($groupid){
        if($groupid == ''){
            return false;
        }
        $key = self::GROUP_PUBDIR_KEY_PREFIX . $groupid;
        return $this->ssdb->hget(self::SSDB_USER_CONFIG_NAME, $key);        
    }

    public function getDefaultDir($groupid, $userid = ''){
        $defaultdir = [];
        if($userid != ''){
            $res = $this->getUserDefaultPubdir($userid);
            $res == false ? '' : $defaultdir = json_decode($res, true);
        }
        if(((!isset($defaultdir['dirid'])) || ($defaultdir['dirid'] == '')) && ($groupid != '')){
            $res = $this->getGroupDefaultPubdir($groupid);
            $res == false ? '' : $defaultdir = json_decode($res, true);            
        }
        if(((!isset($defaultdir['dirid'])) || ($defaultdir['dirid'] == ''))){
            $res = $this->getDefaultPubdir();
            $res == false ? '' : $defaultdir = json_decode($res, true);            
        }
        return $defaultdir;
    }
}
?>

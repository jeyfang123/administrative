<?php
class Config
{
    public static function user(){
        static $userconfig = null;
        if($userconfig === null){
            include_once 'datrix/config.user.class.php';
            $userconfig = new UserConfig();
        }
        return $userconfig;
    }

    public static function pubdir(){
        static $dirconfig = null;
        if($dirconfig === null){
            include_once 'datrix/config.pubdir.class.php';
            $dirconfig = new PubDirConfig();
        }
        return $dirconfig;
    }
}
?>

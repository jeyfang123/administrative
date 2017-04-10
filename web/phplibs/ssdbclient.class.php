<?php
require_once 'vendor/ssdb/ssdb.php';
class SSDBClient
{
    private $ssdb = null;

    function __construct($server = '127.0.0.1', $port = 8881) {
        if($this->ssdb === null){
            try{
                $this->ssdb = new SimpleSSDB($server, $port);
            }catch(SSDBException $e){
                $this->ssdb = null;
            }        
        }
    }

    function set($key, $val){
        if($this->ssdb == null) return false;
        return $this->ssdb->set($key, $val);
    }

    function get($key){
        if($this->ssdb == null) return false;
        return $this->ssdb->get($key);
    }

    function del($key){
        if($this->ssdb == null) return false;
        return $this->ssdb->del($key);        
    }

    function hdel($name, $key){
        if($this->ssdb == null) return false;
        return $this->ssdb->hdel($name, $key);
    }

    function hset($name, $key, $val){
        if($this->ssdb == null) return false;
        return $this->ssdb->hset($name, $key, $val);
    }

    function hget($name, $key){
        if($this->ssdb == null) return false;
        return $this->ssdb->hget($name, $key);
    }

    function hkeys($name, $start_key = '', $end_key = '', $limit = 20){
        if($this->ssdb == null) return false;
        return $this->ssdb->hkeys($name, $start_key, $end_key, $limit);
    }

    function hlist($name, $start_key = '', $end_key = '', $limit = 20){
        if($this->ssdb == null) return false;
        return $this->ssdb->hkeys($name, $start_key, $end_key, $limit);
    }

    function scan($name, $start_key = '', $end_key = '', $limit = 20){
        if($this->ssdb == null) return false;
        return $this->ssdb->hkeys($name, $start_key, $end_key, $limit);
    }

    function rscan($name, $start_key = '', $end_key = '', $limit = 20){
        if($this->ssdb == null) return false;
        return $this->ssdb->hkeys($name, $start_key, $end_key, $limit);
    }

    function hsize($name){
        return $this->ssdb->hsize($name);
    }

    function multiGet($name, $keys){
        return $this->ssdb->multi_hget($name, $keys);        
    }


    // zset
    function zset($name, $key, $score){
        if($this->ssdb == null) return false;
        return $this->ssdb->zset($name, $key, $score);
    }

    function zdel($name, $key){
        if($this->ssdb == null) return false;
        return $this->ssdb->zdel($name, $key);
    }
    function zclear($name){
        if($this->ssdb == null) return false;
        return $this->ssdb->zclear($name);
    }

    function zexists($name, $key){
        if($this->ssdb == null) return false;
        return $this->ssdb->zexists($name, $key);
    }

    function multiZset($name, $keyScores){
        if($this->ssdb == null) return false;
        return $this->ssdb->multi_zset($name, $keyScores);
    }

    function multiZdel($name, $keys){
        if($this->ssdb == null) return false;
        return $this->ssdb->multi_zdel($name, $keys);
    }

    function multiZget($name, $keys){
        if($this->ssdb == null) return false;
        return $this->ssdb->multi_zget($name, $keys);
    }

    function zlist($name_start, $name_end, $limit){
        if($this->ssdb == null) return false;
        return $this->ssdb->zlist($name_start, $name_end, $limit);
    }

    function zrscan($name, $key_start, $score_start, $score_end, $limit){
        if($this->ssdb == null) return false;
        return $this->ssdb->zrscan($name, $key_start, $score_start, $score_end, $limit);
    }
}
?>

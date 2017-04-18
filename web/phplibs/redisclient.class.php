<?php
class RedisClient
{
	private $redis = null;
	
	function __construct() {
        if($this->redis === null){
            try{
                ini_set('default_socket_timeout', -1);
                $this->redis = new Redis();
                $this->redis->connect(REDIS_IP,REDIS_PORT);
                $this->redis->auth(REDIS_PASSWORD);
            }catch(RedisException $e){
                $this->redis = null;
            }        
        }
    }

    function set($key, $val, $date = 7 * 24 * 60 * 60){
        if($this->redis == null) return false;
        return   $this->redis->set($key, $val, $date);
    }

    function setnx($key, $val){
        if($this->redis == null) return false;
        return $this->redis->setnx($key, $val);
    }

    function get($key){
        if($this->redis == null) return false;
        return $this->redis->get($key);
    }

    function del($key){
        if($this->redis == null) return false;
        return $this->redis->delete($key);        
    }

    function ttl($key){
        if($this->redis == null) return false;
        return $this->redis->ttl($key);   
    }

    function expire($key,$time){
        if($this->redis == null) return false;
        return $this->redis->expire($key,$time);
    }

    function appendttl($key, $date = 7 * 24 * 60 * 60){
        if($this->redis == null) return false;
        $keyttl = $this->redis->ttl($key);
        if($keyttl == -2) {
            return false;
        } else {
            $newttl = $keyttl + $date;
            return $this->redis->expire($key, $newttl); 
        }
    }

    function hdel($name, $key){
        if($this->redis == null) return false;
        return $this->redis->hDel($name, $key);
    }

    function hset($name, $key, $val){
        if($this->redis == null) return false;
        return $this->redis->hSet($name, $key, $val);
    }

    function hget($name, $key){
        if($this->redis == null) return false;
        return $this->redis->hGet($name, $key);
    }

    function hkeys($key){
        if($this->redis == null) return false;
        return $this->redis->hKeys($key);
    }


    function rPush($key, $value ) {
        return $this->redis->rPush($key, $value);
    }

    function lRange($key, $start, $end ) {
        return $this->redis->lRange($key, $start, $end);
    }

//    function sRemove( $key, $member1) {
//        return $this->redis->sRemove($key, $member1);
//    }
//
//    function sMove( $srcKey, $dstKey, $member ) {
//        return $this->redis->sMove($srcKey, $dstKey, $member);
//    }
//    function sRem( $key, $member1, $member2 = null, $memberN = null ) {
//        return $this->redis->sRem($key, $member1);
//    }

    function keys($key)
    {
        return $this->redis->keys($key .'*');
    }

    function lRem( $key, $value, $count ) {
        return $this->redis->lRem( $key, $value, $count );
    }
}
?>
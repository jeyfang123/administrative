<?php
/**
 * Created by PhpStorm.
 * User: wangxianjin@pimpin.cn
 * Date: 2016/09/12
 * Time: 13:38
 */
class TokenModel
{
    private $_time_save = 2 * 60 * 60;
    private $_redis = null;
    public $RepeatIP ;
    function __construct()
    {
        $this->_redis = new RedisClient ();
    }

    public function getUser($token)
    {
        $value = $this->getTokenStr($token);
        if (!empty($value))
            return json_decode($value, true);
        return false;
    }

    public function createToken($userId)
    {
        $token = time();
        $token = $userId."_".md5($token . 'administrative');
        $this->_redis->set($token, $userId, $this->_time_save);
        return $token;
    }

    public function getToken($prefix, $user)
    {
        $token = time();
        $token = $prefix . '-' . md5($token . 'administrative');
        $res = $this->_redis->set($token, $user, $this->_time_save);
        if($res !== true){
            echo 'the redis server go away';
            return false;
        }
        return $token;
    }

    // 获取重复登录的ip
    public function getRepeatLoginIP($prefix, $token, $userid)
    {        
        $userid = substr(md5($userid), 2, 6);
        $keys = $this->_redis->keys( $prefix.'-'.$userid.'-*');
        
        if ( count($keys) < 1 )   
            return '';
        foreach ($keys as $k=> $v) {
            if ($v != $token )
            {
                $info = json_decode($this->_redis->get($v), true);
                $res = $this->_redis->del($v);
            }
        }
        return isset($info['clientip']) ? $info['clientip'] : '';
    }


    public function reSaveToken($token, $user = "")
    {
        if (empty($user))
            $user = $this->_redis->get($token);
        if (!empty($user))
            $this->_redis->set($token, $user, $this->_time_save);
    }

    /**
     * @param $token
     * 把token作为键名的redis删掉
     */
    public function redisDel($token)
    {
        return $this->_redis->del($token);
    }

    /**
     * @param $token
     * 获取token作为键名的redis
     */
    public function getTokenStr($token)
    {
        $value = $this->_redis->get($token);
        if ($value)
            $this->_redis->expire($token, $this->_time_save);
        return $value;
    }

    /**
     * @param $indentify code
     * 缓存验证码
     */
    public function identifyCode($phone, $num)
    {
        $this->_redis->set($phone, $num, 5 * 60);
    }

    /**
     * @param $phone
     * 获验证码
     */
    public function getIndentify($phone)
    {
        $indentify = $this->_redis->get($phone);
        if ($indentify) {
            return $indentify;
        }
        return false;
    }

    public function reSetRedis($token, $info)
    {
        $info = json_encode($info);
        $this->_redis->set($token, $info, $this->_time_save);
        return true;
    }

    public function set($key, $value)
    {
        $this->_redis->set($key, $value, 60 * 5);
    }

    public function get($key)
    {
        return $this->_redis->get($key);
    }
}
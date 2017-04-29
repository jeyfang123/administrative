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
    function __construct()
    {
        if($this->_redis == null){
            $this->_redis = new RedisClient ();
        }
    }

    public function ping(){
        return $this->_redis->ping();
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
//        $token = time();
        $token = '111';
        $token = $prefix . '-' . md5($token . 'administrative');
        $res = $this->_redis->set($token, $user, $this->_time_save);
        if($res !== true){
            echo 'the redis server go away';
            return false;
        }
        return $token;
    }

    public function reSaveToken($token, $user = "")
    {
        if (empty($user))
            $user = $this->_redis->get($token);
        if (!empty($user))
            $this->_redis->set($token, $user, $this->_time_save);
    }

    /**
     * 把$key作为键名的redis删掉
     * @param $key
     * @return bool
     */
    public function redisDel($key)
    {
        return $this->_redis->del($key);
    }

    /**
     * 获取token作为键名的redis
     * @param $token
     * @return bool
     */
    public function getTokenStr($token)
    {
        $value = $this->_redis->get($token);
        $this->_redis->expire($token, $this->_time_save);
        return $value;
    }

    /**
     * 缓存验证码
     * @param $phone
     * @param $num
     */
    public function identifyCode($phone, $num)
    {
        $this->_redis->set($phone, $num, 5 * 60);
    }

    /**
     * 获验证码
     * @param $phone
     * @return bool
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
        $expire = 60 * 5;
        if(func_num_args() == 3){
            $expire = func_get_arg(2);
        }
        return $this->_redis->set($key, $value, $expire);
    }

    public function get($key)
    {
        return $this->_redis->get($key);
    }

    public function keys($key){
        return $this->_redis->keys($key);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/28
 * Time: 14:08
 */
define('REDIS_IP',     '127.0.0.1');
define('REDIS_PORT',   '10310');
include_once dirname(__DIR__).'/phplibs/redisclient.class.php';
include_once dirname(__DIR__).'/app/model/public/token.model.php';
$redis = New TokenModel();
echo $redis->set('admin','test redis',2*60*60);
echo $redis->get('admin');
echo $redis->get('admin');
echo $redis->get('admin');
echo $redis->keys('keys *');
//echo $redis->ping();


die();
ini_set('default_socket_timeout', -1);
$redis = new Redis();
var_dump($redis->connect(REDIS_IP,REDIS_PORT));
//var_dump($redis->auth(REDIS_PASSWORD));

$key = $redis->set('admin','test redis',2*60*60);
echo $key;

try{
//    $redis->connect(REDIS_IP,REDIS_PORT);
//    var_dump($redis->auth(REDIS_PASSWORD));
    $value = $redis->get('admin');
    var_dump($value);
}
catch(RedisException $e){
    echo '第二次ping:返回false'."</br>";
}
die();

$key = $redis->getToken('admin','test redis');
echo $key;


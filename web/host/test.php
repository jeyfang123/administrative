<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/28
 * Time: 14:08
 */
define('REDIS_IP',     '112.74.168.39');
define('REDIS_PORT',   '6379');
define('REDIS_PASSWORD','redis123654jey');
include_once dirname(__DIR__).'/phplibs/redisclient.class.php';
include_once dirname(__DIR__).'/app/model/public/token.model.php';
$redis = New TokenModel();
$key = $redis->getToken('admin','test redis');
echo $key;

$value = $redis->getUser($key);
echo $value;
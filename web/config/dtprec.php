<?php
// 定义PHP 常用变量

define('CODE_NOPER',203);
define('CODE_SUCCESS',  200);
define('CODE_PARAMETER_ERROR',400); //参变量错误
define('CODE_NOT_FOUND',405);
define('CODE_ERROR',    300);  //代码错误
define('CODE_RELOGIN',211);
define('CODE_Already_Registered',  212);  //已注册
define('CODE_NOT_REGISTERED',204);  //未注册
define('CODE_NotAdmin',  213);  //非管理员
define('CODE_USER_HAVELOGIN',205);  //用户已登录
define('CODE_PERMISSION_DEND',401);//没有权限
define('SQL_ERROR',-100);  //sql错误
define('ADMINUSER',1);  //管理员编码
define('CODE_RESUMIT',305); //重复提交


// 数据库地址
define('DB_IP',         '127.0.0.1');
define('DB_IP_PORT',    '5432');
define('DB_IP_USERNAME', 'postgres');
define('DB_IP_PASSWORD', 'postgres123654jey');

define('REDIS_IP',     '127.0.0.1');
define('REDIS_PORT',   '10310');

define('ADMIN_UPLOAD_PREFIX', '/upload/admin/');
define('ADMIN_UPLOAD', WEB_ROOT . ADMIN_UPLOAD_PREFIX);
define('ADMIN_AVATAR','admin/img/avatar/');
?>
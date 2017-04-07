<?php
// 定义PHP 常用变量

define('CODE_SUCCESS',  200);
define('CODE_PARAMETER_ERROR',400); //参变量错误
define('CODE_NOT_FOUND',405);
define('CODE_ERROR',    300);  //代码错误
define('CODE_RELOGIN',  211);    //重登录
define('CODE_Already_Registered',  212);  //已注册
define('CODE_NOT_REGISTERED',204);  //未注册
define('CODE_NotAdmin',  213);  //非管理员
define('CODE_USER_HAVELOGIN',205);  //用户已登录
define('CODE_PERMISSION_DEND',401);//没有权限
define('SQL_ERROR',-100);  //sql错误
define('ADMINUSER',1);  //管理员编码
define('APPUSER',2);   //app用户编码
define('CODE_RESUMIT',305); //重复提交

//用户类型（对应token前缀）
define('SUPER_ADMIN','0101');//超级管理员
define('PROPERTY_ADMIN','0201');//物业管理员
define('PROPERTY_APP','0202');//物业app用户
define('STREET_ADMIN','0301');//街道管理员
define('HOUSE_ADMIN','0401');//房管管理员
define('WECHATUSER','1001');  //微信用户编码
define('HUGESCREEN','0501');//大屏用户
define('COMMITTEE_ADMIN','0601');//居委会用户
// 数据库地址
define('DB_IP',         '10.1.7.12');
define('DB_IP_PORT',    '14103');

define('DB_IP_USERNAME', 'postgres');
define('DB_IP_PASSWORD', 'postgres');

define('REDIS_IP',     '10.1.7.12');
define('REDIS_PORT',   10310);


date_default_timezone_set('Asia/Shanghai');
define('ADMIN_UPLOAD_PREFIX', '/upload/admin/');
define('ADMIN_UPLOAD', WEB_ROOT . ADMIN_UPLOAD_PREFIX);
?>
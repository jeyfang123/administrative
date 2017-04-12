<?php
	//创建数据库连接
	class DB{
	 	private static $_instances = [];
        const TB_USER = 'ea_user';

	    private function __construct() {
	    }

	    public static function getInstance ($database = 'easier') {
	        if ( !isset(self::$_instances[$database]) || is_null(self::$_instances[$database]) ) {
				$db = NewADOConnection('pgsql');
				$link = $db->Connect(DB_IP . ':' . DB_IP_PORT, DB_IP_USERNAME, DB_IP_PASSWORD, $database);
				$db->SetFetchMode(ADODB_FETCH_ASSOC);
				self::$_instances[$database] = $db;
	        }
	        return self::$_instances[$database];
	    }

	    public function __clone(){}
	}
?>
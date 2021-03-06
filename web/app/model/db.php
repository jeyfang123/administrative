<?php
	//创建数据库连接
	class DB{
	 	private static $_instances = [];
        const TB_USER = 'ea_user';
        const TB_ROLE_USER = 'role_user';
        const TB_PERMISSION = 'ea_permission';
        const TB_ROLE_PERMISSION = 'role_permission';
        const TB_ROLE = 'ea_role';
        const TB_TYPE = 'process_type';
        const TB_INSTANCE = 'process_instance';
        const TB_PROCESS = 'ea_process';
        const TB_PRO_DETAIL = 'process_detail';
        const TB_PRO_NODE = 'process_node';
        const TB_PRO_FLOW = 'process_flow';
        const TB_CONTENT = 'ea_news';

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

        public static function returnModelRes($res){
            if($res === false || (is_array($res) && reset($res) === false)){
                return false;
            }
            else if(empty($res)){
                return null;
            }
            else{
                return func_get_args();
            }
        }

	    public function __clone(){}
	}
?>
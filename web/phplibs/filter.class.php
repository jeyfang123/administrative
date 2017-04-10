<?php
class Filtros{

	public static function htmlFilter($valor){
		$resultado = htmlentities($valor, ENT_QUOTES,'UTF-8'); // así de sencillo$valor
		return $resultado;
	}
		
	public static function xssFilter($data)
	{
		// Fix &entity\n;
		$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
		$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

		// Remove any attribute starting with "on" or xmlns
		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

		// Remove javascript: and vbscript: protocols
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

		// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

		// Remove namespaced elements (we do not need them)
		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

		do
		{
			// Remove really unwanted tags
			$old_data = $data;
			$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
			}
		while ($old_data !== $data);

		// we are done...
		return $data;
	}
	//
	public static function filter($data){
		// $this->SQLfilter($data);
		// $this->xssFilter($data);
		// $this->htmlFilter($data);
		$newdata = self::SQLfilter($data);
		$newdata = self::xssFilter($newdata);
		$newdata = self::htmlFilter($newdata);
		
		return $newdata;		
	}


	// mysql_real_escape_string
	public static function check_input($value)
	{
		// 若设置为on，则需要反转义
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		// 判断是否为纯数字
		if (!is_numeric($value))
		{
			$value = "'" . mysql_real_escape_string($value) . "'";
		}
		return $value;
	}


	public static function post_check($post) {
		if (!get_magic_quotes_gpc()) { // 判断magic_quotes_gpc是否为打开 
			$post = addslashes($post); // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤 
		} 
		$post = str_replace("_", "\_", $post); // 把 '_'过滤掉 
		$post = str_replace("%", "\%", $post); // 把 '%'过滤掉 
		$post = nl2br($post); // 回车转换 
		$post = htmlspecialchars($post); // html标记转换 

		return $post; 
	}

	public static function verify_sql ($sql_str) {
		return eregi ('select|insert|and|or|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $sql_str); // 进行过滤 
	} 

	/*
	*	过滤数字
	*/
	public static function filter_num ($id = "") {
		if ($id == "") {
			return $id;
		}else if (is_bool ($id)) {
			return $id;
		}else {
			return intval($id);
		}
	}

	/*
	*	过滤bool
	*/
	public static function filter_bool ($id = false) {
		if (is_bool ($id)) {
			return $id;
		}else {
			return false;
		}
	}


	/*
	*	转义字符串
	*/
	public static function filter_str ($str = "") {
		if (!get_magic_quotes_gpc()) { // 判断magic_quotes_gpc是否打开 
			$str = addslashes($str); // 进行过滤
		} 
		$str = str_replace("_", "\_", $str); // 把 '_'过滤掉 
		$str = str_replace("%", "\%", $str); // 把 '%'过滤掉 

		return $str;
	}

	/**
	 * 验证数据中是否存在某些特定的值
	 * @author fushangui
	 * @param subject array
	 * @param search array defalut ['', null, 0, false]
	 * @return bool 
	 */ 
	public static function verifys($subject, $search=['',NULL,false])
	{
		if (!is_array($subject)) return false;
		
		$res = true;
		 array_walk($subject, function($item, $key, $search){
		 	if ( in_array($item, $search)){
		 		$res = false;
		 	}
		 }, $search);

		 return $res;
	}
}

?>

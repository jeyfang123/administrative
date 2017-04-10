<?php
/**
* 	封装多语言
*	zh_cn, en_us, zh_tw
*	可以将语保存到session中。不然每次都会刷新页面都会重新读所有配置
*/
class Lang
{
	/**
    * @describe:	语言存放的路径
    */
	private $_langPath;

	/**
    * @describe:	当前何种语言 $_langList中的一个
    */
	private $_lang;

	/**
    * @describe:	可支持的语言类型
    */
	private $_langList = array ("zh_cn", "en_us", "zh_tw");

	private $_langStr = [];

	/**
    * @param:	$langPath: 语言存放的路径
    */
	function __construct($langPath = "")
	{
		if($this->_lang == ''){
			$this->_lang = $this->_langList[0];
		}
		if($langPath !== $this->_langPath){
			$this->_langPath = $langPath;
			$this->_langStr = [];
		}
	}

	function __destruct (){}

	/**
    * @describe:	加载语言目录下的所有文件
	* @param:	$name 	such as: "log.name"
    */
	public function getText ($name){
		$tmplang = [];
		if(isset($this->_langStr[$this->_lang])){
			$tmplang = $this->_langStr[$this->_lang];
		}
		if (is_string($name) && !empty($name)){
			$arr = explode (".", $name);
			if(count($arr) < 1){
				return '';
			}
			$module = $arr[0];
			$langkey = $arr[1];
			if(isset($tmplang[$module][$langkey])){
				return $tmplang[$module][$langkey];
			}else{
				if (!empty ($this->_langPath)){					
					$fullPath = $this->_langPath . '/' . $this->_lang .'/';
				}else{
					$fullPath = $this->_lang .'/';
				}
				$fullPath .= $module . '.php';
				$tmp = include ($fullPath);
				$this->_langStr[$this->_lang][$module] = $tmp;
				if(isset($tmp[$langkey])){
					return $tmp[$langkey];
				}
				return $langkey;
			}
		}
		return "";
	}


	/**
    * @describe:	加载语言目录下的对应的文件
    */
	public function loadLang ($langName){
		if (empty($langName))
			return;

		if (!empty ($this->_langPath))
			$fullPath = $this->_langPath . '/' . $this->_lang .'/'.$langName.'.php';
		else
			$fullPath = $this->_lang .'/'.$langName.'.php';


		// 判断路径是否存在

		if (!file_exists ($fullPath))
			return;
		else
			return include ($fullPath);
			// return include_once ($fullPath);
	}

	/**
    * @describe:	获取可支持的语言
    */
	public function getLangList (){
		return $this->_langList;
	}	
	
	/**
    * @describe:	设置语言类型
	* @param: $lang 
    */
	public function setLang ($lang = ""){
		if (in_array($lang , $this->_langList)) {
			$this->_lang = $lang;
		}
	}

	/**
    * @describe:	设置语言路径
	* @param: $lang 
    */
	public function setLangPath ($langPath){
		if($langPath !== $this->_langPath){
			$this->_langPath = $langPath;
			$this->_langStr = [];
		}
	}

	public function getLang (){
		return $this->_lang;
	}

	public function getLangPath (){
		return $this->_langPath;
	}
}
?>
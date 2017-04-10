<?php
/**
* 封装twig
*/
require_once dirname(__FILE__) . '/../' . 'vendor/Twig/Autoloader.php';
class Twig
{
	/**
    * @describe: Twig的保存路径
    */
	private $_twig;
	private $_twigRoute;

	/**
    * @describe: 模板路径，模板缓存路径
    */
	private $_templatesRoute;
	private $_templatesCacheRoute;

	/**
    * @describe: Twig 环境配置
    */
	private $_twigEnvironment;
	private $_vars;


	function __construct($templatesPath = "")
	{
		$this->_twigEnvironment = array ();
		// $this->_twigRoute = $twigPath;
		$this->_templatesRoute = $templatesPath;
		$this->_vars = array ();
	}

	function __destruct (){

	}

	public function setTemplatesPath ($path){
		$this->_templatesRoute = $path;
	}

	public function setTemplatesCachePath ($path){
 		$this->_templatesCacheRoute = $path;
	}

	public function setEnvironment ($environmentName, $value){
		if (!empty ($this->_templatesCacheRoute)) {
			$this->_twigEnvironment["cache"] = $this->_templatesCacheRoute;
		}
	}

	private function _init (){
		Twig_Autoloader::register ();
		$loader = new Twig_Loader_Filesystem ($this->_templatesRoute);

		$this->setEnvironment ("cache", $this->_templatesCacheRoute);
		$this->_twig = new Twig_Environment($loader, $this->_twigEnvironment);

        /*	添加自定义过滤器
        $filter = new Twig_SimpleFilter('sizeFormat', function ($size) {
            return Util::formatCapacity($size);
        });
        $this->_twig->addFilter($filter);
        */
	}
		
	public function render ($page){		
		if(!$this->_twig){
			$this->_init();
		}
		return $this->_twig->render($page, $this->_vars);
	}

	public function assign ( $var, $val ){
		$this->_vars[$var] = $val;
	}	
		
	public function getTwigPath (){
		return $this->_twigRoute;
	}

	public function getTemplatesPath (){
		return $this->_templatesRoute;
	}
	
	public function getTemplatesCachePath (){
		return $this->_templatesCacheRoute;
	}

	public function getEnvironment (){
		return $this->_twigEnvironment;
	}

}
?>
<?php
class Controller {
    protected $_twig;
    protected $_lang;

    function __construct(){
        $this->_twig = new Twig (PRODUCT_TEMPLATE_DIR);
        $this->_lang = new Lang (PRODUCT_PHP_LANG_DIR);

        $this->_getLangType ();
    }

    function viewTpl ($tplname, $data = []){
        foreach($data as $name => $var){
            $this->_twig->assign ($name, $var);
        }

        return $this->_twig->render ($tplname);
    }



    private function _getLangType () {
        $langType = Session::get('lang');
        if($langType === ''){
            $langType = 'zh_cn';
        }else if($langType == '2'){
            $langType = 'en_us';
        }else if($langType == '1'){
            $langType = 'zh_cn';
        }
        
        $this->_lang->setLang($langType);

        $this->_twig->assign ("langType", $langType);
        $this->_twig->assign ("publicLang", $this->_lang->loadLang ("public"));
    }


    private function _getLoginUser () {

    }

    protected static function _getMyPage () {
        return strtolower (static::_getMyClass ());
    }

    protected static function _getMyClass () {
        return __CLASS__;
    }

    protected function returnJson($body=[])
    {
        return json_encode( $body, JSON_UNESCAPED_UNICODE);
    }
}
?>
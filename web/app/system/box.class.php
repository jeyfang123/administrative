<?php
/**
* 加载指定类型的类程序
**/
class Box
{
    public static $_modelObjArr;

    /*
     * $_className:类名，（$_appName文件名去掉.controller.php或.model.php）
     * $_className 全部小写之后就是 $_appName
     * $_typeStr(controller 或者 model)
     * $product(工程名$_className在controller或者model下的那个文件夹下)
     *
     * */
    public static function getObject($_className, $_typeStr='controller', $product = '') {
        $_appName = strtolower($_className);
        $_typeStr = strtolower($_typeStr);
        $_className = $_appName.ucfirst ($_typeStr);
        if( isset(self::$_modelObjArr[$_className]) && is_object(self::$_modelObjArr[$_className]) ){
            return self::$_modelObjArr[$_className];
        }
        $appdir = ($product === '') ? PRODUCT : $product;
        $file = WEB_BASE . "/app/${_typeStr}/".$appdir."/${_appName}.${_typeStr}.php";
        if (file_exists($file)){
            include_once $file;
            if (class_exists($_className)) {
                return self::_createObject($_className);
            }
        }
        return null;
    }

    public static function controller($classname, $product){
        return self::getObject($classname, 'controller', $product);
    }

    public static function model($classname, $product){
        return self::getObject($classname, 'model', $product);
    }

    public static function _createObject($_className) {
        if ( isset(self::$_modelObjArr[$_className]) && is_object(self::$_modelObjArr[$_className]) ) {
            return self::$_modelObjArr[$_className];
        } else {
            self::$_modelObjArr[$_className] = new $_className();
            return self::$_modelObjArr[$_className];
        }
    }
    //错误提示
    public static function _showErr($_errTypeStr='') {
        echo $_errTypeStr;
        exit;
        //errorlog($_errTypeStr);
    }
}//end class
?>

<?php
class Loader
{
    static function loadOEMInfo($epath) {
        $lang = Session::get("lang");
        $file = $epath . '/zh_cn.php';
        if($lang != "") $file = $epath . '/'.$lang.'.php';
        return Loader::_loadFile($file);
    }

    static function lang() {
        $lang = Session::get("lang");
        $file = dirname(__DIR__) . '/../website/lang/zh_cn.php';
        if ($lang != "") {
            $file = dirname(__DIR__) . '/../website/lang/' . $lang . '.php';
        }
        return Loader::_loadFile($file);
    }

    static function _loadFile($file) {
        if (!file_exists ($file)) {
            return false;
        }
        // 将文件内容放在缓冲中，暂时不输出
        ob_start ();
            include $file;
            $value = ob_get_contents();
        ob_end_clean ();
        return $value;
    }
}
?>

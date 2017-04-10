<?php
class Util
{
    public static function errorExit($code = CGI_ERROR, $message = '') {
        $result = [
            'code' => $code,
            'msg' => $message
        ];
        echo json_encode($result);
        exit();
    }
    public static function post($key, $default = "") {
        $value = $default;
        if (isset($_POST[$key]) && ($_POST[$key] !== '')) {
            $data = $_POST[$key];
            $value = $data;
        }
        return $value;
    }

    public static function get($key, $default = "") {
        $value = $default;
        if (isset($_GET[$key]) && ($_GET[$key] !== '')) {
            $data = $_GET[$key];
            $value = $data;
        }
        return $value;
    }

    public static function formatCapacity($capacity) {
        $str = "";
        if ($capacity == 0 || $capacity == "") {
            $str = "0";
        } else {
            if (($capacity / (1024*1024*1024*1024*1024)) >= 1) { // PB
                $str = ((floor(($capacity /(1024*1024*1024*1024*1024))*100))/100). "P";//100保留两位
            } else if (($capacity / (1024*1024*1024*1024)) >= 1) { // T
                $str = ((floor(($capacity /(1024*1024*1024*1024))*100))/100). "T";//100保留两位
            } else if ($capacity / (1024*1024*1024) >= 1) { //G
                $str = ((floor(($capacity /(1024*1024*1024))*100))/100) . "G";
            } else if ($capacity / (1024*1024) >= 1) { //M
                $str = ((floor(($capacity /(1024*1024))*100))/100) . "M";
            } else if ($capacity / (1024) >= 1) {       //k
                $str = ((floor(($capacity /(1024))*100))/100) . "K";
            } else if ($capacity > 0) {
                $str = $capacity . "B";
            }
        }
        return $str;
    }

    public static function jsondecode($jsonstr, $toarray = true){
        if ($jsonstr == '') {
            return '';
        }
        $encode = mb_detect_encoding($jsonstr, "ASCII, UTF-8, GB2312, GBK, BIG5, ISO-8859-1, UTF-16");
        $utf8str = '';
        if ($encode != 'UTF-8' && $encode != 'ASCII') {
            $arr = split(',', $jsonstr);
            foreach($arr as $str){
                $encode = mb_detect_encoding($str, "ASCII, UTF-8, GB2312, GBK, BIG5, ISO-8859-1, UTF-16");
                $cleanstr = $str;
                if ($encode != 'UTF-8' && $encode != 'ASCII') {
                    $cleanstr = mb_convert_encoding($str, 'UTF-8', $encode);
                }
                if($utf8str != ''){
                    $utf8str .= ',';
                }
                $utf8str .= $cleanstr;
            }
        }
        if($utf8str == ''){
            $utf8str = $jsonstr;
        }
        return json_decode($utf8str, $toarray);
    }

    public static function JSONPrepare($json) {
        // This will convert ASCII/ISO-8859-1 to UTF-8.
        // Be careful with the third parameter (encoding detect list), because
        // if set wrong, some input encodings will get garbled (including UTF-8!)
        $input = mb_convert_encoding($json, 'UTF-8', 'auto');

        // Remove UTF-8 BOM if present, json_decode() does not like it.
        if (substr($input, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) {
            $input = substr($input, 0, 3);
        }
        return $input;
    }

    public static function generateUUid(){
        $str = substr(md5(time()), 0, 6);
        return $str;
    }

    public static function getOemInfo(){
        $info = file_get_contents(OEM_CONF_FILE);
        $info = json_decode($info, true);
        if($info['product'] == ''){
            $info['product'] = 'DATRIX';
        }
        if($info['title'] == ''){
            $info['title'] = 'DATRIX-非结构化数据管理平台';
        }
        if($info['logo'] == ''){
            $info['logo'] = 'default';
        }
        if($info['company'] == ''){
            $info['company'] = '上海德拓信息技术有限公司';
        }
        if(!isset($info['logolist']['default'])){
            $image = file_get_contents(WEB_ROOT . '/css/image/logo.png');
            $base64 = 'data:image/png' . ';base64,' . base64_encode($image);

            $info['logolist']['default'] = ['name' => 'DATRIX', 'path' => '/css/image/logo.png', 'base64' => $base64];
        }

        return $info;
    }


    public static function catalogFormatTime($time){
        $hour = 0;
        $minute = 0;
        $second = 0;
        $frame = $time * 1000;

        $hour = floor($time / 3600);
        $time = floor($time % 3600);
        $minute = floor($time / 60);
        $time = floor($time % 60);
        $second = $time;
        $framestr = floor(round($frame % 1000, 5) * 25 / 1000);
        if($hour < 10){
            $hour = '0'.$hour;
        }
        if($minute < 10){
            $minute = '0'.$minute;
        }
        if($second < 10){
            $second = '0'.$second;
        }
        if($framestr < 10){
            $framestr = '0'.$framestr;
        }
        return $hour.':'.$minute.':'.$second.':'.$framestr;
    }
}
?>

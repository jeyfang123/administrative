<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/17
 * Time: 11:47
 */

class CommonController extends Controller{
    public function noPermission(){
        return $this->viewTpl ('noPermission.html');
    }

    /**
     * 时间转换函数，将目标时间转换为汉字表达方式，比如几分钟前、几小时前等
     * @param $time
     * @return string $str
     */
    static function convertTimeToHuman($time) {
        $time = strtotime($time);
        $rtime2 = date("Y-m-d H:i",$time);
        //$htime = date("H:i",$time);
        $htime='';
        $time = time() - $time;
        if ($time < 60) {
            $str = '刚刚';
        }
        elseif ($time < 3600) {
            $min = floor($time/60);
            $str = $min.' 分钟前';
        }
        elseif ($time < 3600 * 24) {
            $h = floor($time/(3600));
            $str = $h.'小时前 '.$htime;
        }
        elseif ($time < 3600 * 24 * 3) {
            $d = floor($time/(60*60*24));
            if($d==1)
                $str = '昨天 '.$htime;
            else
                $str = '前天 '.$htime;
        }
        else {
            $str = $rtime2;
        }
        return $str;

    }

    /**
     * 上传图片
     * @param $req
     * @return array
     */
    public function imgUpload($req){
        $uploadFiles = $req->files();
        $type = $req->param('type');
        $indexName = '';
        $errorMsg = '';
        switch ($type){
            case 'roleuser':
                $savePath = ADMIN_AVATAR;
                $indexName = 'avatar';
                break;
            case 'content':
                $savePath = ADMIN_CONTENT;
                $indexName = 'file';
                break;
            default:
                $savePath = null;
                break;
        }
        if($uploadFiles[$indexName]['error'] > 0){
            $errorMsg = '文件上传失败';
            return $this->returnJson(['code'=>CODE_ERROR,'error'=>$errorMsg]);
        }
        $type = pathinfo($uploadFiles[$indexName]['name'],PATHINFO_EXTENSION);
        $size = $uploadFiles[$indexName]['size'];
        if($size > 1024*1024*2){
            $errorMsg = '文件过大';
            return $this->returnJson(['code'=>CODE_ERROR,'error'=>$errorMsg]);
        }
        if(!in_array($type ,['img','png','jpg','jpeg'])){
            $errorMsg = '文件类型不支持';
            return $this->returnJson(['code'=>CODE_ERROR,'error'=>$errorMsg]);
        }
        if(!getimagesize ($uploadFiles[$indexName]['tmp_name'])){
            $errorMsg = '请上传图片文件';
            return $this->returnJson(['code'=>CODE_ERROR,'error'=>$errorMsg]);
        }
        $fileName = md5($uploadFiles[$indexName]['name'].strtotime('now')).'.'.$type;

        $this->createDir(WEB_ROOT.DIRECTORY_SEPARATOR.$savePath);

        $saveFile = move_uploaded_file($uploadFiles[$indexName]['tmp_name'], WEB_ROOT.DIRECTORY_SEPARATOR.$savePath.$fileName);

        if($saveFile !== true){
            $errorMsg = '保存文件失败';
            return $this->returnJson(['code'=>CODE_ERROR,'error'=>$errorMsg]);
        }
        else{
            return $this->returnJson(['code'=>CODE_SUCCESS,'file'=>DIRECTORY_SEPARATOR.$savePath.$fileName]);
        }
    }

    /**
     * 创建目录
     * @param $path
     */
    private function createDir($path){
        if(!file_exists($path)){
            mkdir($path,0777,true);
            chmod($path,0777);
        }
    }
}
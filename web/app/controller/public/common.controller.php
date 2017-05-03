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
     * 上传图片
     * @param $req
     * @return array
     */
    public function imgUpload($req){
        $uploadFiles = $req->files();
        $type = $req->param('type');
        $errorMsg = '';
        switch ($type){
            case 'roleuser':
                $savePath = ADMIN_AVATAR;
                break;
            default:
                $savePath = null;
                break;
        }
        if($uploadFiles['avatar']['error'] > 0){
            $errorMsg = '文件上传失败';
            return $this->returnJson(['code'=>CODE_ERROR,'error'=>$errorMsg]);
        }
        $type = pathinfo($uploadFiles['avatar']['name'],PATHINFO_EXTENSION);
        $size = $uploadFiles['avatar']['size'];
        if($size > 1024*1024*2){
            $errorMsg = '文件过大';
            return $this->returnJson(['code'=>CODE_ERROR,'error'=>$errorMsg]);
        }
        if(!in_array($type ,['img','png','jpg'])){
            $errorMsg = '文件类型不支持';
            return $this->returnJson(['code'=>CODE_ERROR,'error'=>$errorMsg]);
        }
        if(!getimagesize ($uploadFiles['avatar']['tmp_name'])){
            $errorMsg = '请上传图片文件';
            return $this->returnJson(['code'=>CODE_ERROR,'error'=>$errorMsg]);
        }
        $fileName = md5($uploadFiles['avatar']['name'].strtotime('now')).'.'.$type;

        $this->createDir(WEB_ROOT.'/'.$savePath);

        $saveFile = move_uploaded_file($uploadFiles['avatar']['tmp_name'], WEB_ROOT.'/'.$savePath.$fileName);

        if($saveFile !== true){
            $errorMsg = '保存文件失败';
            return $this->returnJson(['code'=>CODE_ERROR,'error'=>$errorMsg]);
        }
        else{
            return $this->returnJson(['code'=>CODE_SUCCESS,'file'=>$savePath.$fileName]);
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
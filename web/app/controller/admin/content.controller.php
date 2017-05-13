<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/5/9
 * Time: 17:23
 */
class ContentController extends Controller{

    public function attention(){
        return $this->viewTpl('attention.html');
    }

    public function interpretation(){
        return $this->viewTpl('interpretation.html');
    }

    public function searchContent($req){
        $key = $req->param("keywords",'');
        $page = $req->param('page',1);
        $pageSize = $req->param('pageSize',15);
        $type = $req->param('type','1');
        $res = Box::getObject('content','model','admin')->getContent($key,$type,$page,$pageSize);
        if($res == false){
            return $this->returnJson(['code'=>CODE_ERROR,'msg'=>'获取失败']);
        }
        else if($res == null){
            return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>[],'total'=>0]);
        }
        $commonObj = Box::getObject('common','controller','public');
        array_walk($res['data'],function(&$item) use ($commonObj){
            $item['publish_time'] = $commonObj::convertTimeToHuman($item['publish_time']);
            $item['contents'] = mb_substr(strip_tags(htmlspecialchars_decode($item['contents'])),0,200);
        });
        return $this->returnJson(['code'=>CODE_SUCCESS,'data'=>$res['data'],'count'=>$res['total']]);
    }

    public function detail($req){
        $id = $req->param('id');
        $res = Box::getObject('content','model','admin')->contentDetail($id);
        $commonObj = Box::getObject('common','controller','public');
        $res['publish_time'] = $commonObj::convertTimeToHuman($res['publish_time']);
        $res['contents'] = htmlspecialchars_decode($res['contents']);
        return $this->viewTpl('article.html',['data'=>$res]);
    }

    public function addContent($req){
        $title = Filtros::post_check($req->param('title'));
        $contents = htmlspecialchars($req->param('contents'));
        $types = $req->param('types');
        $grandson = $req->param("grandson");
        $coverInput = $req->param('cover');
        $cover = null;
        if(!empty($coverInput)){
            $cover = json_decode(Box::getObject('common','controller','public')->imgUpload($req),true);
            if($cover['code'] == CODE_ERROR){
                echo '上传让图片失败:'.$cover['error'];
                exit();
            }
            $cover = $cover['file'];
        }

        $content = [
            'title'=>$title,
            'content'=>$contents,
            'type'=>$types,
            'grandson'=>$grandson,
            'cover'=>$cover
        ];
        $res = Box::getObject('content','model','admin')->addContent($content,$req->user->depart_user_id);
        if($types == '1'){
            return $this->viewTpl('attention.html');
        }
        else{
            return $this->viewTpl('interpretation.html');
        }
    }

}